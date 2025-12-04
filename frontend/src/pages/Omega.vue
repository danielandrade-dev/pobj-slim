<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import Icon from '../components/Icon.vue'
import { useOmega } from '../composables/useOmega'
import { useOmegaFilters } from '../composables/useOmegaFilters'
import { useOmegaBulk } from '../composables/useOmegaBulk'
import { useOmegaRender } from '../composables/useOmegaRender'
import OmegaFilterPanel from '../components/OmegaFilterPanel.vue'
import OmegaHeader from '../components/omega/OmegaHeader.vue'
import OmegaSidebar from '../components/omega/OmegaSidebar.vue'
import OmegaToolbar from '../components/omega/OmegaToolbar.vue'
import OmegaTable from '../components/omega/OmegaTable.vue'
import OmegaBulkPanel from '../components/omega/OmegaBulkPanel.vue'
import OmegaDrawer from '../components/omega/OmegaDrawer.vue'
import OmegaTicketModal from '../components/omega/OmegaTicketModal.vue'
import OmegaTeamManager from '../components/omega/OmegaTeamManager.vue'
import '../assets/omega.css'

const route = useRoute()

const omega = useOmega()
const filters = useOmegaFilters()
const bulk = useOmegaBulk(omega)
const render = useOmegaRender(omega, filters, bulk)

const pageRoot = ref<HTMLElement | null>(null)
const mainContentRef = ref<HTMLElement | null>(null)
const sidebarCollapsed = ref(false)
const searchQuery = ref('')

async function loadOmegaData() {
  // Verifica se estamos usando componentes Vue (OmegaTable)
  const pageElement = pageRoot.value
  const container = pageElement?.querySelector('.omega-table-container')
  const isUsingVueComponents = !!container
  
  // Mostra loading state apenas se não estiver usando componentes Vue
  if (pageElement && !isUsingVueComponents) {
    showLoadingState(pageElement)
  }
  
  try {
    const data = await omega.loadInit()
    if (!data) {
      if (!isUsingVueComponents) {
        hideLoadingState(pageElement)
      }
      return
    }
    
    // Aguarda o conteúdo ser renderizado
    nextTick(() => {
      setTimeout(() => {
        if (!isUsingVueComponents) {
          hideLoadingState(pageElement)
          renderOmegaData()
        } else {
          // Se estiver usando componentes Vue, apenas limpa qualquer skeleton que possa ter sido inserido
          const tbody = pageElement?.querySelector('#omega-ticket-rows')
          if (tbody && tbody.querySelector('.omega-skeleton-row')) {
            tbody.innerHTML = ''
          }
        }
      }, 100)
    })
  } catch (err) {
    if (!isUsingVueComponents) {
      hideLoadingState(pageElement)
      showErrorState(pageElement)
    }
  }
}

function showLoadingState(root: HTMLElement | null) {
  if (!root) return
  
  // Verifica se estamos usando componentes Vue (OmegaTable)
  const container = root.querySelector('.omega-table-container')
  if (container) {
    // Não manipula o DOM se estiver usando componentes Vue
    // O componente Vue gerencia o estado de loading
    return
  }
  
  const mainContent = root.querySelector('.omega-main__content')
  if (mainContent) {
    mainContent.classList.add('omega-loading')
    const tbody = root.querySelector('#omega-ticket-rows')
    if (tbody) {
      tbody.innerHTML = `
        ${Array.from({ length: 5 }).map(() => `
          <tr class="omega-skeleton-row">
            <td class="col-select"><div class="omega-skeleton omega-skeleton--checkbox"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text" style="width: 80%"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--badge"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--text"></div></td>
            <td><div class="omega-skeleton omega-skeleton--badge"></div></td>
          </tr>
        `).join('')}
      `
    }
  }
}

function hideLoadingState(root: HTMLElement | null) {
  if (!root) return
  
  const mainContent = root.querySelector('.omega-main__content') || mainContentRef.value?.querySelector('.omega-main__content')
  if (mainContent) {
    mainContent.classList.remove('omega-loading')
  }
}

function showErrorState(root: HTMLElement | null) {
  if (!root) return
  
  const tbody = root.querySelector('#omega-ticket-rows') || mainContentRef.value?.querySelector('#omega-ticket-rows')
  if (tbody) {
    tbody.innerHTML = `
      <tr>
        <td colspan="12" class="omega-empty-state">
          <div class="omega-empty-state__content">
            <Icon name="alert-circle" :size="48" style="color: var(--brad-color-error); margin-bottom: 16px;" />
            <h3>Erro ao carregar dados</h3>
            <p>Não foi possível carregar os chamados. Tente novamente.</p>
            <button class="omega-btn omega-btn--primary" onclick="location.reload()" style="margin-top: 16px;">
              <Icon name="refresh" :size="18" />
              <span>Recarregar</span>
            </button>
          </div>
        </td>
      </tr>
    `
  }
}

function renderOmegaData() {
  // Verifica se estamos usando componentes Vue (OmegaTable)
  const pageElement = document.getElementById('omega-page')
  const container = pageElement?.querySelector('.omega-table-container')
  const isUsingVueComponents = !!container
  
  // Se estiver usando componentes Vue, não chama renderOmegaData
  // pois o Vue já gerencia a renderização
  if (!isUsingVueComponents) {
    render.renderOmegaData()
  }
  applySidebarState()
}

function applySidebarState() {
  // Sidebar state is now handled by the component itself
}

function handleNavClick(viewId: string) {
  // Se for o gerenciador de analistas, abre o modal
  if (viewId === 'team-edit-analyst') {
    teamManagerOpen.value = true
    return
  }
  
  omega.setCurrentView(viewId as any)
  nextTick(() => {
    renderOmegaData()
  })
}

function handleSearch(value: string) {
  searchQuery.value = value
  renderOmegaData()
}

function handleFilterToggle() {
  filters.toggleFilterPanel()
}

function handleClearFilters() {
  filters.resetFilters()
  renderOmegaData()
}

function handleRefresh() {
  omega.clearCache()
  omega.loadInit().then(() => {
    renderOmegaData()
  })
}

const drawerOpen = ref(false)
const drawerInitialData = ref<{ department?: string; type?: string; observation?: string } | undefined>(undefined)
const ticketModalOpen = ref(false)
const selectedTicketId = ref<string | null>(null)
const teamManagerOpen = ref(false)

function handleNewTicket(initialData?: { department?: string; type?: string; observation?: string }) {
  drawerInitialData.value = initialData
  drawerOpen.value = true
}

// Função para construir observação a partir dos detalhes do nó
function buildObservationFromDetail(detail: any): string {
  if (!detail || !detail.node) return ''
  
  const node = detail.node
  const parts: string[] = []
  
  if (node.label) {
    parts.push(`Item: ${node.label}`)
  }
  
  if (node.level) {
    parts.push(`Nível: ${node.level}`)
  }
  
  if (node.summary) {
    const summary = node.summary
    if (summary.valor_realizado) {
      parts.push(`Realizado: R$ ${summary.valor_realizado.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`)
    }
    if (summary.valor_meta) {
      parts.push(`Meta: R$ ${summary.valor_meta.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`)
    }
    if (summary.atingimento_p !== undefined) {
      parts.push(`Atingimento: ${summary.atingimento_p.toFixed(1)}%`)
    }
  }
  
  if (node.detail) {
    const detailInfo = node.detail
    if (detailInfo.gerente) {
      parts.push(`Gerente: ${detailInfo.gerente}`)
    }
    if (detailInfo.canal_venda) {
      parts.push(`Canal: ${detailInfo.canal_venda}`)
    }
  }
  
  return parts.join('\n')
}

// Função para mostrar toast
function showOmegaToast(message: string, tone: 'success' | 'info' | 'warning' | 'danger' = 'success') {
  if (!message) return
  const root = pageRoot.value
  if (!root) return
  const container = root.querySelector('#omega-toast-stack') as HTMLElement
  if (!container) return
  
  const icons: Record<string, string> = {
    success: 'ti ti-check',
    info: 'ti ti-info-circle',
    warning: 'ti ti-alert-triangle',
    danger: 'ti ti-alert-circle'
  }
  
  const icon = icons[tone] || icons.info
  const toast = document.createElement('div')
  toast.className = `omega-toast omega-toast--${tone}`
  toast.setAttribute('role', 'status')
  toast.innerHTML = `<i class="${icon}" aria-hidden="true"></i><span>${escapeHTML(message)}</span>`
  container.appendChild(toast)
  
  requestAnimationFrame(() => {
    toast.setAttribute('data-visible', 'true')
  })
  
  const lifetime = 3600
  setTimeout(() => {
    toast.setAttribute('data-visible', 'false')
    setTimeout(() => {
      if (toast.parentElement === container) toast.remove()
    }, 220)
  }, lifetime)
  
  while (container.children.length > 3) {
    const first = container.firstElementChild
    if (!first || first === toast) break
    first.remove()
  }
}

// Função para escapar HTML
function escapeHTML(value: string): string {
  if (!value) return ''
  const div = document.createElement('div')
  div.textContent = value
  return div.innerHTML
}

// Função principal para criar novo ticket
async function handleNewTicketSubmit(data: any) {
  const user = omega.currentUser.value
  if (!user) {
    showOmegaToast('Usuário não encontrado.', 'danger')
    return
  }
  
  const requesterName = user.name?.trim() || ''
  
  // Constrói o subject
  const subjectParts: string[] = []
  if (data.type) subjectParts.push(data.type)
  if (requesterName) subjectParts.push(requesterName)
  const subject = subjectParts.length ? subjectParts.join(' • ') : 'Chamado Omega'
  
  try {
    const now = new Date().toISOString()
    
    // Se o departamento for "Matriz", o chamado vai para a fila (sem ownerId)
    // Qualquer analista da Matriz pode pegar e atribuir depois
    const isMatriz = data.department === 'Matriz'
    const ownerId = isMatriz 
      ? null  // Chamados da Matriz vão para a fila sem dono
      : (['analista', 'supervisor', 'admin'].includes(user.role) ? user.id : null)
    
    const newTicket = {
      subject,
      company: requesterName,
      requesterName,
      productId: '',
      product: data.type || '',
      family: '',
      section: '',
      queue: data.department || 'Matriz',  // Garante que sempre seja Matriz
      status: 'aberto',
      category: data.type,
      priority: 'media' as const,
      opened: now,
      updated: now,
      dueDate: null,
      requesterId: user.id,
      ownerId: ownerId,
      teamId: user.teamId || null,
      context: {
        diretoria: '',
        gerencia: '',
        agencia: '',
        ggestao: '',
        gerente: '',
        familia: '',
        secao: '',
        prodsub: data.type || ''
      },
      history: [{
        date: now,
        actorId: user.id,
        action: 'Abertura do chamado',
        comment: data.observation,
        status: 'aberto'
      }],
      attachments: data.attachments?.map((att: any) => att.name) || [],
      flow: data.flow || null
    }
    
    // Chama API para criar ticket
    const { createOmegaTicket } = await import('../api/modules/omega.api')
    const response = await createOmegaTicket(newTicket)
    
    if (response.success && response.data) {
      // Atualiza lista de tickets
      await omega.loadInit()
      
      // Fecha o drawer
      drawerOpen.value = false
      
      // Mostra toast de sucesso
      showOmegaToast('Chamado registrado com sucesso.', 'success')
      
      // Re-renderiza dados
      renderOmegaData()
    } else {
      showOmegaToast(response.error || 'Não foi possível registrar o chamado.', 'danger')
    }
  } catch (err) {
    showOmegaToast('Erro ao registrar o chamado. Tente novamente.', 'danger')
  }
}

function handleBulkStatus() {
  bulk.bulkPanelOpen.value = !bulk.bulkPanelOpen.value
}

function handleTicketClick(ticketId: string) {
  selectedTicketId.value = ticketId
  ticketModalOpen.value = true
}

function handleSelectAll(checked: boolean) {
  if (checked) {
    const tickets = omega.tickets.value
    tickets.forEach((ticket) => {
      bulk.selectedTicketIds.value.add(ticket.id)
    })
  } else {
    bulk.selectedTicketIds.value.clear()
  }
}

function handleBulkApply(status: string) {
  bulk.handleBulkStatusSubmit(status)
  renderOmegaData()
}

function handleFilterApply() {
  renderOmegaData()
}

function handleFilterClear() {
  renderOmegaData()
}

// Observa mudanças nos dados e re-renderiza
// Nota: Quando usando componentes Vue (OmegaTable), não precisamos chamar renderOmegaData
// pois o Vue já gerencia a reatividade automaticamente
watch(() => omega.tickets.value, () => {
  nextTick(() => {
    const pageElement = document.getElementById('omega-page')
    const container = pageElement?.querySelector('.omega-table-container')
    const isUsingVueComponents = !!container
    if (!isUsingVueComponents) {
      renderOmegaData()
    }
  })
}, { deep: true })

watch(() => omega.users.value, () => {
  nextTick(() => {
    const pageElement = document.getElementById('omega-page')
    const container = pageElement?.querySelector('.omega-table-container')
    const isUsingVueComponents = !!container
    if (!isUsingVueComponents) {
      renderOmegaData()
    }
  })
}, { deep: true })

watch(() => omega.currentUserId.value, () => {
  nextTick(() => {
    const pageElement = document.getElementById('omega-page')
    const container = pageElement?.querySelector('.omega-table-container')
    const isUsingVueComponents = !!container
    if (!isUsingVueComponents) {
      renderOmegaData()
    }
  })
})

watch(() => omega.currentView.value, () => {
  nextTick(() => {
    const pageElement = document.getElementById('omega-page')
    const container = pageElement?.querySelector('.omega-table-container')
    const isUsingVueComponents = !!container
    if (!isUsingVueComponents) {
      renderOmegaData()
    }
  })
})

// Verifica se há parâmetros na URL para abrir drawer com dados iniciais
onMounted(() => {
  loadOmegaData()
  
  // Verifica se há parâmetros na query string
  const params = new URLSearchParams(window.location.search)
  const openDrawer = params.get('openDrawer') === 'true' || params.get('intent') === 'new-ticket'
  const department = params.get('queue') || params.get('preferredQueue') || 'POBJ'
  const observation = params.get('observation') || ''
  
  if (openDrawer) {
    nextTick(() => {
      handleNewTicket({
        department,
        observation
      })
    })
  }
})

onBeforeUnmount(() => {
  // Cleanup se necessário
})
</script>

<template>
  <div
    ref="pageRoot"
    id="omega-page"
    class="omega-page"
    data-omega-standalone
  >
    <div id="omega-toast-stack" class="omega-toast-stack" aria-live="polite" aria-atomic="true"></div>
    
    <OmegaHeader
      :omega="omega"
      :show-close="false"
    />

    <div class="omega-body" :data-sidebar-collapsed="sidebarCollapsed ? 'true' : 'false'">
      <OmegaSidebar
        :omega="omega"
        :collapsed="sidebarCollapsed"
        @update:collapsed="sidebarCollapsed = $event"
        @nav-click="handleNavClick"
      />

      <section class="omega-main">
        <div id="omega-breadcrumb" class="omega-breadcrumb"></div>
        <div id="omega-context" class="omega-context" hidden></div>

        <OmegaToolbar
          :omega="omega"
          :filters="filters"
          :bulk="bulk"
          @search="handleSearch"
          @filter-toggle="handleFilterToggle"
          @clear-filters="handleClearFilters"
          @refresh="handleRefresh"
          @new-ticket="handleNewTicket"
          @bulk-status="handleBulkStatus"
        />

        <div id="omega-summary" class="omega-summary" aria-live="polite"></div>

        <div ref="mainContentRef" class="omega-main__content">
          <div id="omega-dashboard" class="omega-dashboard" hidden aria-live="polite"></div>
          <OmegaTable
            :omega="omega"
            :filters="filters"
            :bulk="bulk"
            :search-query="searchQuery"
            @ticket-click="handleTicketClick"
            @select-all="handleSelectAll"
            @new-ticket="handleNewTicket"
          />
        </div>

      </section>
    </div>
    
    <OmegaDrawer
      :omega="omega"
      :open="drawerOpen"
      :initial-data="drawerInitialData"
      @update:open="drawerOpen = $event"
      @submit="handleNewTicketSubmit"
    />
    
    <OmegaFilterPanel
      :omega="omega"
      :filters="filters"
      :open="filters.filterPanelOpen.value"
      @update:open="filters.toggleFilterPanel($event)"
      @apply="handleFilterApply"
      @clear="handleFilterClear"
    />
    
    <OmegaTicketModal
      :omega="omega"
      :open="ticketModalOpen"
      :ticket-id="selectedTicketId"
      @update:open="ticketModalOpen = $event"
    />
    
    <OmegaTeamManager
      :omega="omega"
      :open="teamManagerOpen"
      @update:open="teamManagerOpen = $event"
    />
    
    <OmegaBulkPanel
      :omega="omega"
      :bulk="bulk"
      @close="bulk.bulkPanelOpen.value = false"
      @cancel="handleSelectAll(false)"
      @apply="handleBulkApply"
    />
  </div>
</template>

<style scoped>
.omega-page {
  min-height: calc(100vh - 66px);
  width: 100%;
  display: flex;
  flex-direction: column;
  background: var(--omega-bg, #f6f7fc);
}

.omega-body {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.omega-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.omega-main__content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
}
</style>

