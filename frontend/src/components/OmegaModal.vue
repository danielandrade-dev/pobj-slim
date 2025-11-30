<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useOmega } from '../composables/useOmega'
import { useOmegaFilters } from '../composables/useOmegaFilters'
import { useOmegaBulk } from '../composables/useOmegaBulk'
import { useOmegaFullscreen } from '../composables/useOmegaFullscreen'
import { useOmegaRender } from '../composables/useOmegaRender'
import OmegaFilterPanel from './OmegaFilterPanel.vue'
import OmegaHeader from './omega/OmegaHeader.vue'
import OmegaSidebar from './omega/OmegaSidebar.vue'
import OmegaToolbar from './omega/OmegaToolbar.vue'
import OmegaTable from './omega/OmegaTable.vue'
import OmegaBulkPanel from './omega/OmegaBulkPanel.vue'
import '../assets/omega.css'

interface Props {
  modelValue?: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'close'): void
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false
})

const emit = defineEmits<Emits>()

const omega = useOmega()
const filters = useOmegaFilters()
const bulk = useOmegaBulk(omega)
const fullscreen = useOmegaFullscreen()
const isFullscreen = computed(() => {
  const modal = modalRoot.value
  return modal ? modal.classList.contains('omega-modal--fullscreen') : false
})
const render = useOmegaRender(omega, filters, bulk)

const isOpen = ref(false)
const modalRoot = ref<HTMLElement | null>(null)
const mainContentRef = ref<HTMLElement | null>(null)
const sidebarCollapsed = ref(false)
const searchQuery = ref('')

const addedBodyClasses = ['has-omega-open']

// Estado compartilhado globalmente para permitir acesso externo
if (typeof window !== 'undefined') {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const globalAny = window as any
  if (!globalAny.__omegaModalState) {
    globalAny.__omegaModalState = { isOpen: ref(false) }
  }
}

function updateModalVisibility(open: boolean) {
  isOpen.value = open
}

watch(() => props.modelValue, (newValue) => {
  console.log('👀 props.modelValue mudou para:', newValue)
  updateModalVisibility(newValue)
  if (typeof window !== 'undefined') {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const globalAny = window as any
    if (globalAny.__omegaModalState) {
      globalAny.__omegaModalState.isOpen.value = newValue
    }
  }
  if (newValue) {
    console.log('✅ Modal aberto, carregando dados...')
    ensureBodyState()
    loadOmegaData()
    nextTick(() => {
      if (isOpen.value) {
        renderMainContent()
      }
    })
  } else {
    resetBodyState()
  }
}, { immediate: true })

// Observa mudanças nos dados e re-renderiza
// Nota: Não precisa re-renderizar tickets/users quando usando componentes Vue (eles são reativos)
watch(() => omega.tickets.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      // Apenas renderiza partes que não são componentes Vue
      renderOmegaData()
    })
  }
}, { deep: true })

watch(() => omega.users.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      // Apenas renderiza partes que não são componentes Vue
      renderOmegaData()
    })
  }
}, { deep: true })

watch(() => omega.currentUserId.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      // Apenas renderiza partes que não são componentes Vue
      renderOmegaData()
    })
  }
})

watch(() => omega.currentView.value, () => {
  if (isOpen.value) {
    // Não precisa chamar renderOmegaData() aqui porque OmegaTable é reativo
    // Apenas renderiza partes que não são componentes Vue
    nextTick(() => {
      renderOmegaData()
    })
  }
})

// Watch o estado global também
if (typeof window !== 'undefined') {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const globalAny = window as any
  if (globalAny.__omegaModalState) {
    watch(globalAny.__omegaModalState.isOpen, (newValue) => {
      if (isOpen.value !== newValue) {
        updateModalVisibility(newValue)
        emit('update:modelValue', newValue)
        if (newValue) {
          ensureBodyState()
          loadOmegaData()
        } else {
          resetBodyState()
        }
      }
    })
  }
}

function ensureBodyState() {
  if (typeof document === 'undefined') return
  addedBodyClasses.forEach((cls) => document.body.classList.add(cls))
}

function resetBodyState() {
  if (typeof document === 'undefined') return
  addedBodyClasses.forEach((cls) => document.body.classList.remove(cls))
}

async function loadOmegaData() {
  console.log('🔄 Carregando dados do Omega...')
  
  // Mostra loading state
  const modalElement = modalRoot.value
  if (modalElement) {
    showLoadingState(modalElement)
  }
  
  try {
    const data = await omega.loadInit()
    console.log('✅ Dados do Omega carregados:', data)
    if (!data) {
      console.warn('⚠️ Nenhum dado retornado do Omega')
      hideLoadingState(modalElement)
      return
    }
    
    // Aguarda o conteúdo ser renderizado
    nextTick(() => {
      setTimeout(() => {
        hideLoadingState(modalElement)
        renderOmegaData()
      }, 100)
    })
  } catch (err) {
    console.error('❌ Erro ao carregar dados do Omega:', err)
    hideLoadingState(modalElement)
    showErrorState(modalElement)
  }
}

function showLoadingState(root: HTMLElement | null) {
  if (!root) return
  
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
            <i class="ti ti-alert-circle" style="font-size: 48px; color: var(--brad-color-error); margin-bottom: 16px;"></i>
            <h3>Erro ao carregar dados</h3>
            <p>Não foi possível carregar os chamados. Tente novamente.</p>
            <button class="omega-btn omega-btn--primary" onclick="location.reload()" style="margin-top: 16px;">
              <i class="ti ti-refresh"></i>
              <span>Recarregar</span>
            </button>
          </div>
        </td>
      </tr>
    `
  }
}

function renderOmegaData() {
  render.renderOmegaData()
  applySidebarState()
}

function setSidebarCollapsed(collapsed: boolean) {
  sidebarCollapsed.value = !!collapsed
  applySidebarState()
}

function applySidebarState() {
  // Sidebar state is now handled by the component itself
}

function handleNavClick(viewId: string) {
  omega.setCurrentView(viewId as any)
  // Não precisa chamar renderOmegaData() porque OmegaTable é reativo
  // Apenas renderiza partes que não são componentes Vue (sidebar, etc)
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

function handleNewTicket() {
  const drawer = modalRoot.value?.querySelector('#omega-drawer') as HTMLElement
  if (drawer) {
    drawer.removeAttribute('hidden')
    drawer.hidden = false
    populateFormOptions(modalRoot.value!)
  }
}

function handleBulkStatus() {
  bulk.bulkPanelOpen.value = !bulk.bulkPanelOpen.value
}

function handleTicketClick(ticketId: string) {
  // TODO: Implementar abertura do modal de detalhes do ticket
  console.log('Ticket clicado:', ticketId)
}

function handleSelectAll(checked: boolean) {
  if (checked) {
    // Seleciona todos os tickets da página atual
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

function handleFullscreenToggle() {
  // Fullscreen is handled by the header component
}

// Funções de renderização movidas para useOmegaRender composable

// Função para atualizar lista (igual ao código antigo)
function refreshTicketList(button: HTMLElement) {
  setButtonLoading(button, true)
  
  // Limpa cache e recarrega
  omega.clearCache()
  omega.loadInit()
    .then(() => {
      renderOmegaData()
    })
    .catch((err) => {
      console.warn('⚠️ Falha ao atualizar chamados Omega:', err)
    })
    .finally(() => {
      setButtonLoading(button, false)
    })
}

function setButtonLoading(button: HTMLElement, loading: boolean) {
  if (!button) return
  if (button instanceof HTMLButtonElement || button instanceof HTMLInputElement) {
    button.disabled = !!loading
  }
  if (loading) {
    button.setAttribute('data-loading', 'true')
  } else {
    button.removeAttribute('data-loading')
  }
  const icon = button.querySelector('i')
  if (!icon) return
  if (loading) {
    icon.setAttribute('data-original-icon', icon.className)
    icon.className = 'ti ti-loader-2'
  } else if (icon.getAttribute('data-original-icon')) {
    icon.className = icon.getAttribute('data-original-icon') || 'ti ti-refresh'
    icon.removeAttribute('data-original-icon')
  }
}

// Funções de filtros movidas para useOmegaFilters composable
function setupFilterControls(root: HTMLElement) {
  const filterToggle = root.querySelector('#omega-filters-toggle')
  const filterForm = root.querySelector('#omega-filter-form')
  const clearFiltersBtn = root.querySelector('#omega-clear-filters')
  const clearFiltersTop = root.querySelector('#omega-clear-filters-top')

  filterToggle?.addEventListener('click', () => {
    filters.toggleFilterPanel()
    const modalElement = document.getElementById('omega-modal')
    if (modalElement) {
      let panel = modalElement.querySelector('#omega-filter-panel') as HTMLElement
      // Se o painel não está no body, move para o body para garantir z-index correto acima do header da tabela
      if (panel && panel.parentElement !== document.body) {
        document.body.appendChild(panel)
      }
      const toggle = modalElement.querySelector('#omega-filters-toggle')
      if (panel) panel.hidden = !filters.filterPanelOpen.value
      if (toggle) toggle.setAttribute('aria-expanded', filters.filterPanelOpen.value ? 'true' : 'false')
      if (filters.filterPanelOpen.value) {
        // Re-renderiza estrutura para garantir que os selects estejam populados ANTES de sincronizar
        render.renderStructure(modalElement)
        filters.syncFilterFormState(modalElement)
      }
      if (toggle) {
        toggle.setAttribute('data-active', filters.hasActiveFilters() ? 'true' : 'false')
      }
    }
  })

  filterForm?.addEventListener('submit', (ev) => {
    ev.preventDefault()
    filters.applyFiltersFromForm(root)
    renderOmegaData()
  })

  clearFiltersBtn?.addEventListener('click', () => {
    filters.resetFilters()
    filters.syncFilterFormState(root)
    renderOmegaData()
  })

  clearFiltersTop?.addEventListener('click', () => {
    filters.resetFilters()
    filters.syncFilterFormState(root)
    renderOmegaData()
  })

  // Atualiza estado inicial do botão
  if (filterToggle) {
    filterToggle.setAttribute('data-active', filters.hasActiveFilters() ? 'true' : 'false')
  }
}

function handleFilterApply() {
  renderOmegaData() // Re-renderiza após aplicar filtros
}

function handleFilterClear() {
  renderOmegaData() // Re-renderiza após limpar filtros
}

// Função para obter departamentos disponíveis para o usuário
function getAvailableDepartmentsForUser(user: any): string[] {
  if (!user) return []
  
  // Obtém departamentos únicos da estrutura
  const departments = new Map<string, string>()
  omega.structure.value.forEach((item) => {
    if (item.departamento && !departments.has(item.departamento)) {
      departments.set(item.departamento, item.departamento_id || item.departamento)
    }
  })
  
  const allDepartments = Array.from(departments.keys())
  
  // Se for admin, retorna todos
  if (user.role === 'admin') return allDepartments
  
  // Se for usuário comum, filtra Matriz se não tiver acesso
  if (user.role === 'usuario') {
    return user.matrixAccess ? allDepartments : allDepartments.filter((d) => d !== 'Matriz')
  }
  
  // Para outros roles, retorna todos por enquanto
  return allDepartments
}

// Função para obter tipos de chamado por departamento
function getTicketTypesForDepartment(department: string): string[] {
  if (!department) return []
  
  const types = new Set<string>()
  omega.structure.value.forEach((item) => {
    if (item.departamento === department && item.tipo) {
      types.add(item.tipo)
    }
  })
  
  return Array.from(types).sort()
}

// Função para sincronizar opções de tipo baseado no departamento
function syncTicketTypeOptions(container: HTMLElement, department: string, options: { preserveSelection?: boolean; selectedType?: string } = {}) {
  const typeSelect = container.querySelector('#omega-form-type') as HTMLSelectElement
  if (!typeSelect) return
  
  const types = getTicketTypesForDepartment(department)
  const previousValue = options.preserveSelection ? (options.selectedType || typeSelect.value || '') : (options.selectedType || '')
  
  if (types.length) {
    const placeholder = '<option value="">Selecione o tipo de chamado</option>'
    typeSelect.innerHTML = [
      placeholder,
      ...types.map((type) => `<option value="${escapeHTML(type)}">${escapeHTML(type)}</option>`)
    ].join('')
    
    const nextValue = previousValue && types.includes(previousValue) ? previousValue : ''
    if (nextValue) {
      typeSelect.value = nextValue
    } else {
      typeSelect.value = ''
      typeSelect.selectedIndex = 0
    }
  } else {
    typeSelect.innerHTML = '<option value="" disabled>Selecione um departamento primeiro</option>'
    typeSelect.value = ''
  }
  
  typeSelect.disabled = !types.length
}

// Função para escapar HTML
function escapeHTML(value: string): string {
  if (!value) return ''
  const div = document.createElement('div')
  div.textContent = value
  return div.innerHTML
}

// Função para popular opções do formulário de novo ticket
function populateFormOptions(root: HTMLElement) {
  const form = root.querySelector('#omega-form')
  if (!form) return
  
  const user = omega.currentUser.value
  if (!user) return
  
  const departmentSelect = form.querySelector('#omega-form-department') as HTMLSelectElement
  const requesterDisplay = form.querySelector('#omega-form-requester')
  
  // Atualiza nome do solicitante
  if (requesterDisplay) {
    requesterDisplay.textContent = user.name || '—'
  }
  
  // Popula departamentos
  if (departmentSelect) {
    const departments = getAvailableDepartmentsForUser(user)
    const previous = departmentSelect.value
    
    if (departments.length) {
      const placeholder = '<option value="">Selecione um departamento</option>'
      departmentSelect.innerHTML = [
        placeholder,
        ...departments.map((dept) => `<option value="${escapeHTML(dept)}">${escapeHTML(dept)}</option>`)
      ].join('')
      
      const nextValue = previous && departments.includes(previous) ? previous : ''
      if (nextValue) {
        departmentSelect.value = nextValue
      } else {
        departmentSelect.value = ''
        departmentSelect.selectedIndex = 0
      }
    } else {
      departmentSelect.innerHTML = '<option value="" disabled>Nenhum departamento disponível</option>'
      departmentSelect.value = ''
    }
    
    departmentSelect.disabled = !departments.length
    
    // Remove listeners antigos e adiciona novo listener para mudança de departamento
    const newDeptSelect = departmentSelect.cloneNode(true) as HTMLSelectElement
    departmentSelect.parentNode?.replaceChild(newDeptSelect, departmentSelect)
    
    newDeptSelect.addEventListener('change', () => {
      syncTicketTypeOptions(form as HTMLElement, newDeptSelect.value, { preserveSelection: false })
    })
    
    // Sincroniza tipos iniciais
    syncTicketTypeOptions(form as HTMLElement, newDeptSelect.value, { preserveSelection: true })
  }
}

// Funções de bulk movidas para useOmegaBulk composable

// Funções de fullscreen movidas para useOmegaFullscreen composable

function openModal() {
  console.log('🚀 Abrindo modal Omega...')
  isOpen.value = true
  emit('update:modelValue', true)
  if (typeof window !== 'undefined') {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const globalAny = window as any
    if (globalAny.__omegaModalState) {
      globalAny.__omegaModalState.isOpen.value = true
    }
  }
  ensureBodyState()
  
  // Força a atualização da visibilidade primeiro
  nextTick(() => {
    updateModalVisibility(true)
    
    // Renderiza o conteúdo principal primeiro
    renderMainContent()
    
    // Aplica estado inicial da sidebar
    nextTick(() => {
      applySidebarState()
      
      // Se os dados já estão carregados, renderiza imediatamente
      if (omega.initData.value && omega.tickets.value.length > 0) {
        setTimeout(() => {
          renderOmegaData()
        }, 100)
      } else {
        // Caso contrário, carrega os dados
        loadOmegaData()
      }
    })
  })
}

function closeModal() {
  updateModalVisibility(false)
  emit('update:modelValue', false)
  emit('close')
  if (typeof window !== 'undefined') {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const globalAny = window as any
    if (globalAny.__omegaModalState) {
      globalAny.__omegaModalState.isOpen.value = false
    }
  }
  resetBodyState()
}

function registerGlobalOpener() {
  if (typeof window === 'undefined') return
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const globalAny = window as any
  
  console.log('🔧 Registrando funções globais do Omega')
  
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  globalAny.__openOmegaFromVue = (detail?: any) => {
    console.log('🔓 __openOmegaFromVue chamado', detail)
    if (!omega.isLoading.value) {
      openModal()
    } else {
      console.warn('⚠️ Omega ainda está carregando.')
    }
  }
  
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  globalAny.openOmegaModule = (detail?: any) => {
    console.log('🔓 openOmegaModule chamado', detail)
    openModal()
  }
  
  globalAny.openOmega = globalAny.openOmegaModule
  
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  globalAny.closeOmega = () => {
    console.log('🔒 closeOmega chamado')
    closeModal()
  }
  
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  globalAny.closeOmegaModule = () => {
    console.log('🔒 closeOmegaModule chamado')
    closeModal()
  }
  
  console.log('✅ Funções globais registradas:', {
    __openOmegaFromVue: typeof globalAny.__openOmegaFromVue,
    openOmegaModule: typeof globalAny.openOmegaModule,
    openOmega: typeof globalAny.openOmega,
    closeOmega: typeof globalAny.closeOmega,
    closeOmegaModule: typeof globalAny.closeOmegaModule
  })
}

function unregisterGlobalOpener() {
  if (typeof window === 'undefined') return
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const globalAny = window as any
  if (globalAny.__openOmegaFromVue) {
    delete globalAny.__openOmegaFromVue
  }
  if (globalAny.openOmegaModule) {
    delete globalAny.openOmegaModule
  }
  if (globalAny.openOmega) {
    delete globalAny.openOmega
  }
  if (globalAny.closeOmega) {
    delete globalAny.closeOmega
  }
  if (globalAny.closeOmegaModule) {
    delete globalAny.closeOmegaModule
  }
}

function renderMainContent() {
  // Aguarda o ref estar disponível
  if (!mainContentRef.value) {
    console.warn('⚠️ mainContentRef não está disponível ainda, tentando novamente...')
    nextTick(() => {
      setTimeout(() => {
        renderMainContent()
      }, 50)
    })
    return
  }
  
  console.log('✅ Renderizando drawer do Omega')
  
  // Renderiza apenas o drawer (toolbar, tabela e bulk panel agora são componentes Vue)
  const contentHtml = `
    <div id="omega-drawer" class="omega-drawer" hidden>
      <div class="omega-drawer__overlay" data-omega-drawer-close></div>
      <section class="omega-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="omega-drawer-title">
        <header class="omega-drawer__header">
          <div>
            <h3 id="omega-drawer-title">Novo chamado</h3>
            <p id="omega-drawer-desc">Preencha os campos abaixo para registrar o chamado com agilidade.</p>
          </div>
          <button class="omega-icon-btn" type="button" data-omega-drawer-close aria-label="Fechar formulário">
            <i class="ti ti-x"></i>
          </button>
        </header>
        <div id="omega-form-feedback" class="omega-feedback" role="alert" hidden></div>
        <form id="omega-form" class="omega-form">
          <div class="omega-field omega-field--static" aria-live="polite">
            <span class="omega-field__label">Solicitante</span>
            <div id="omega-form-requester" class="omega-field__static">—</div>
          </div>
          <div class="omega-form__grid">
            <label class="omega-field" for="omega-form-department">
              <span class="omega-field__label">Departamento</span>
              <select id="omega-form-department" class="omega-select" required></select>
            </label>
            <label class="omega-field" for="omega-form-type">
              <span class="omega-field__label">Tipo de chamado</span>
              <select id="omega-form-type" class="omega-select" required></select>
            </label>
          </div>
          <label class="omega-field" for="omega-form-observation">
            <span class="omega-field__label">Observação</span>
            <textarea id="omega-form-observation" class="omega-textarea" rows="6" placeholder="Descreva a demanda" required></textarea>
          </label>
          <section id="omega-form-flow" class="omega-form-flow" hidden aria-hidden="true" data-omega-flow>
            <header class="omega-form-flow__header">
              <h4>Fluxo de aprovações</h4>
              <p>Esta transferência exige os de acordo dos gerentes listados abaixo.</p>
            </header>
            <div class="omega-form-flow__approvals">
              <article class="omega-flow-approval">
                <div class="omega-flow-approval__icon" aria-hidden="true"><i class="ti ti-user-check"></i></div>
                <div class="omega-flow-approval__body">
                  <span class="omega-flow-approval__label">Gerente da agência solicitante</span>
                  <label class="omega-field" for="omega-flow-requester-name">
                    <span class="omega-field__label">Nome completo</span>
                    <input id="omega-flow-requester-name" class="omega-input" type="text" placeholder="Informe o gerente responsável"/>
                  </label>
                  <label class="omega-field" for="omega-flow-requester-email">
                    <span class="omega-field__label">E-mail corporativo</span>
                    <input id="omega-flow-requester-email" class="omega-input" type="email" placeholder="nome.sobrenome@empresa.com"/>
                  </label>
                </div>
              </article>
              <article class="omega-flow-approval">
                <div class="omega-flow-approval__icon" aria-hidden="true"><i class="ti ti-building-bank"></i></div>
                <div class="omega-flow-approval__body">
                  <span class="omega-flow-approval__label">Gerente da agência cedente</span>
                  <label class="omega-field" for="omega-flow-target-name">
                    <span class="omega-field__label">Nome completo</span>
                    <input id="omega-flow-target-name" class="omega-input" type="text" placeholder="Informe o gerente responsável"/>
                  </label>
                  <label class="omega-field" for="omega-flow-target-email">
                    <span class="omega-field__label">E-mail corporativo</span>
                    <input id="omega-flow-target-email" class="omega-input" type="email" placeholder="nome.sobrenome@empresa.com"/>
                  </label>
                </div>
              </article>
            </div>
            <p class="omega-form-flow__hint">Após os dois de acordo, o chamado entra automaticamente na fila de Encarteiramento.</p>
          </section>
          <div class="omega-field omega-field--attachments">
            <span class="omega-field__label" id="omega-form-file-label">Arquivos</span>
            <input id="omega-form-file" class="sr-only" type="file" multiple aria-labelledby="omega-form-file-label"/>
            <div class="omega-attachments">
              <div class="omega-attachments__actions">
                <button class="omega-btn" type="button" data-omega-add-file>
                  <i class="ti ti-paperclip"></i>
                  <span>Adicionar arquivo</span>
                </button>
              </div>
              <ul id="omega-form-attachments" class="omega-attachments__list" aria-live="polite"></ul>
            </div>
          </div>
          <input id="omega-form-product" type="hidden"/>
          <input id="omega-form-subject" type="hidden"/>
          <footer class="omega-form__actions">
            <button class="omega-btn" type="button" data-omega-drawer-close>Cancelar</button>
            <button class="omega-btn omega-btn--primary" type="submit">
              <i class="ti ti-device-floppy"></i>
              <span>Salvar</span>
            </button>
          </footer>
        </form>
      </section>
    </div>
  `
  
  mainContentRef.value.innerHTML = contentHtml
  
  // Setup dos listeners do drawer após renderizar
  nextTick(() => {
    const modalElement = modalRoot.value
    if (!modalElement) return
    
    // Adiciona listeners para fechar o drawer
    const drawerCloseButtons = modalElement.querySelectorAll('[data-omega-drawer-close]')
    drawerCloseButtons.forEach((btn) => {
      const closeHandler = () => {
        const drawer = modalElement.querySelector('#omega-drawer') as HTMLElement
        if (drawer) {
          drawer.hidden = true
          drawer.setAttribute('hidden', '')
        }
      }
      btn.removeEventListener('click', closeHandler)
      btn.addEventListener('click', closeHandler)
    })
    
    // Listener para overlay do drawer
    const drawerOverlay = modalElement.querySelector('.omega-drawer__overlay')
    if (drawerOverlay) {
      const overlayHandler = () => {
        const drawer = modalElement.querySelector('#omega-drawer') as HTMLElement
        if (drawer) {
          drawer.hidden = true
          drawer.setAttribute('hidden', '')
        }
      }
      drawerOverlay.removeEventListener('click', overlayHandler)
      drawerOverlay.addEventListener('click', overlayHandler)
    }
  })
}

onMounted(() => {
  registerGlobalOpener()
  
  if (props.modelValue) {
    openModal()
  }
})

onBeforeUnmount(() => {
  unregisterGlobalOpener()
  resetBodyState()
  fullscreen.cleanup()
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      id="omega-modal"
      ref="modalRoot"
      class="omega-modal"
      :class="{ 'omega-modal--fullscreen': isFullscreen }"
      data-omega-standalone
    >
      <div class="omega-modal__overlay" @click="closeModal"></div>
      <section class="omega-modal__panel" role="dialog" aria-modal="true" aria-labelledby="omega-title">
        <div id="omega-toast-stack" class="omega-toast-stack" aria-live="polite" aria-atomic="true"></div>
        
        <OmegaHeader
          :omega="omega"
          :fullscreen="fullscreen"
          @close="closeModal"
          @fullscreen-toggle="handleFullscreenToggle"
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

            <div class="omega-main__content">
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

            <!-- Drawer ainda renderizado via DOM por enquanto -->
            <div ref="mainContentRef" class="omega-main__content-wrapper" style="display: none;"></div>
          </section>
        </div>
      </section>
    </div>
    
    <OmegaFilterPanel
      :omega="omega"
      :filters="filters"
      :open="filters.filterPanelOpen.value"
      @update:open="filters.toggleFilterPanel($event)"
      @apply="handleFilterApply"
      @clear="handleFilterClear"
    />
  </Teleport>

  <Teleport to="body">
    <OmegaBulkPanel
      :omega="omega"
      :bulk="bulk"
      @close="bulk.bulkPanelOpen.value = false"
      @cancel="handleSelectAll(false)"
      @apply="handleBulkApply"
    />
  </Teleport>
</template>

<style scoped>
/* Garante que o modal sempre seja um overlay fixo, mesmo com omega-standalone */
:deep(#omega-modal) {
  position: fixed !important;
  inset: 0 !important;
  z-index: 2200 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 24px !important;
  pointer-events: auto !important;
}

:deep(#omega-modal[hidden]) {
  display: none !important;
  pointer-events: none !important;
}

/* Garante que o overlay seja visível */
:deep(#omega-modal .omega-modal__overlay) {
  display: block !important;
}

/* Estilos para o modo tela cheia */
:deep(#omega-modal.omega-modal--fullscreen) {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  max-width: 100vw !important;
  max-height: 100vh !important;
  padding: 0 !important;
  margin: 0 !important;
  border-radius: 0 !important;
  box-shadow: none !important;
}

:deep(#omega-modal.omega-modal--fullscreen .omega-modal__panel) {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  max-width: 100vw !important;
  max-height: 100vh !important;
  margin: 0 !important;
  border-radius: 0 !important;
  box-shadow: none !important;
}
</style>

