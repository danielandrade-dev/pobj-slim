<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useOmega } from '../composables/useOmega'
import { useOmegaFilters } from '../composables/useOmegaFilters'
import { useOmegaBulk } from '../composables/useOmegaBulk'
import { useOmegaFullscreen } from '../composables/useOmegaFullscreen'
import { useOmegaRender } from '../composables/useOmegaRender'
import omegaTemplate from '../legacy/omega-template.html?raw'
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
const render = useOmegaRender(omega, filters, bulk)

const isOpen = ref(false)
const modalRoot = ref<HTMLElement | null>(null)
const omegaTemplateHtml = omegaTemplate
const sidebarCollapsed = ref(false)

const addedBodyClasses = ['omega-standalone', 'has-omega-open']

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
  
  function tryUpdateVisibility() {
    const modalElement = document.getElementById('omega-modal')
    if (modalElement) {
      if (open) {
        modalElement.removeAttribute('hidden')
        modalElement.hidden = false
        return true
      } else {
        modalElement.setAttribute('hidden', '')
        modalElement.hidden = true
        return true
      }
    }
    return false
  }
  
  // Tenta atualizar imediatamente
  if (!tryUpdateVisibility()) {
    // Se não encontrou, tenta novamente no próximo tick
    nextTick(() => {
      if (!tryUpdateVisibility()) {
        // Se ainda não encontrou, tenta após um pequeno delay
        setTimeout(() => {
          tryUpdateVisibility()
        }, 100)
      }
    })
  }
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
  } else {
    resetBodyState()
  }
}, { immediate: true })

// Observa mudanças nos dados e re-renderiza
watch(() => omega.tickets.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      renderOmegaData()
    })
  }
}, { deep: true })

watch(() => omega.users.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      renderOmegaData()
    })
  }
}, { deep: true })

watch(() => omega.currentUserId.value, () => {
  if (isOpen.value) {
    nextTick(() => {
      renderOmegaData()
    })
  }
})

watch(() => omega.currentView.value, () => {
  if (isOpen.value) {
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
  const modalElement = document.getElementById('omega-modal')
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
    
    // Aguarda o template HTML ser renderizado
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
  
  const mainContent = root.querySelector('.omega-main__content')
  if (mainContent) {
    mainContent.classList.remove('omega-loading')
  }
}

function showErrorState(root: HTMLElement | null) {
  if (!root) return
  
  const tbody = root.querySelector('#omega-ticket-rows')
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
  const root = document.getElementById('omega-modal')
  if (!root) return
  
  const collapsed = sidebarCollapsed.value
  const body = root.querySelector('.omega-body')
  const sidebar = root.querySelector('#omega-sidebar')
  const toggle = root.querySelector('#omega-sidebar-toggle')
  
  if (body) {
    body.setAttribute('data-sidebar-collapsed', collapsed ? 'true' : 'false')
  }
  
  if (sidebar) {
    sidebar.setAttribute('data-collapsed', collapsed ? 'true' : 'false')
  }
  
  if (toggle) {
    toggle.setAttribute('data-collapsed', collapsed ? 'true' : 'false')
    toggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false')
    toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true')
    const label = collapsed ? 'Expandir menu' : 'Recolher menu'
    toggle.setAttribute('aria-label', label)
    toggle.setAttribute('title', label)
    const icon = toggle.querySelector('i')
    if (icon) {
      icon.className = collapsed ? 'ti ti-chevron-right' : 'ti ti-chevron-left'
    }
  }
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
    
    // Aplica estado inicial da sidebar
    nextTick(() => {
      applySidebarState()
    })
    
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

onMounted(() => {
  registerGlobalOpener()
  
  // Aguarda a renderização do template HTML
  nextTick(() => {
    // Aguarda um pouco mais para garantir que o v-html foi processado
    setTimeout(() => {
      const modalElement = document.getElementById('omega-modal')
      if (modalElement) {
        // Remove o atributo hidden inicial se o modal deve estar aberto
        if (props.modelValue || isOpen.value) {
          modalElement.removeAttribute('hidden')
          modalElement.hidden = false
        }
        
        // Adiciona listeners para fechar o modal
        const closeButtons = modalElement.querySelectorAll('[data-omega-close]')
        closeButtons.forEach((btn) => {
          btn.addEventListener('click', closeModal)
        })
        
        const overlay = modalElement.querySelector('.omega-modal__overlay')
        if (overlay) {
          overlay.addEventListener('click', closeModal)
        }
        
        // Adiciona listener para botão de refresh (igual ao código antigo)
        const refreshButton = modalElement.querySelector('#omega-refresh')
        if (refreshButton) {
          refreshButton.addEventListener('click', () => {
            refreshTicketList(refreshButton as HTMLElement)
          })
        }
        
        // Adiciona listeners para filtros
        setupFilterControls(modalElement)
        
        // Adiciona listeners para bulk status
        bulk.setupBulkControls(modalElement, renderOmegaData)
        
        // Adiciona listener para toggle da sidebar
        const sidebarToggle = modalElement.querySelector('#omega-sidebar-toggle')
        if (sidebarToggle) {
          sidebarToggle.addEventListener('click', () => {
            setSidebarCollapsed(!sidebarCollapsed.value)
          })
          // Aplica estado inicial
          applySidebarState()
        }
        
        // Adiciona listener para botão de novo ticket
        const newTicketButton = modalElement.querySelector('#omega-new-ticket')
        if (newTicketButton) {
          newTicketButton.addEventListener('click', () => {
            console.log('➕ Abrindo formulário de novo ticket...')
            const drawer = modalElement.querySelector('#omega-drawer')
            if (drawer) {
              drawer.removeAttribute('hidden')
            }
          })
        }
        
        // Bind dos controles de tela cheia
        const setupFullscreenControls = () => {
          const fsBtn = modalElement.querySelector('[data-omega-fullscreen-toggle]') as HTMLElement || 
                        modalElement.querySelector('#omega-fullscreen') as HTMLElement
          
          if (fsBtn) {
            console.log('✅ Botão encontrado, configurando fullscreen controls...')
            fullscreen.bindOmegaFullscreenControls(modalElement)
            return true
          }
          return false
        }
        
        // Tenta configurar imediatamente
        if (!setupFullscreenControls()) {
          console.log('⏳ Botão não encontrado, aguardando...')
          
          // Usa MutationObserver para detectar quando o botão é adicionado
          const observer = new MutationObserver((mutations, obs) => {
            if (setupFullscreenControls()) {
              obs.disconnect()
              console.log('✅ Fullscreen controls configurados via MutationObserver')
            }
          })
          
          observer.observe(modalElement, {
            childList: true,
            subtree: true
          })
          
          // Timeout de segurança
          setTimeout(() => {
            observer.disconnect()
            if (!setupFullscreenControls()) {
              console.warn('⚠️ Não foi possível configurar fullscreen controls após timeout')
            }
          }, 2000)
        }
      }
    }, 50)
  })
  
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
      ref="modalRoot"
      class="omega-modal-wrapper"
      v-html="omegaTemplateHtml"
    ></div>
  </Teleport>
</template>

<style scoped>
.omega-modal-wrapper {
  position: fixed;
  inset: 0;
  z-index: 2200;
  pointer-events: none;
}

.omega-modal-wrapper :deep(#omega-modal) {
  position: fixed;
  inset: 0;
  z-index: 2200;
  pointer-events: auto;
}

.omega-modal-wrapper :deep(#omega-modal[hidden]) {
  display: none !important;
  pointer-events: none;
}

/* Estilos para o modo tela cheia */
.omega-modal-wrapper :deep(#omega-modal.omega-modal--fullscreen) {
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

.omega-modal-wrapper :deep(#omega-modal.omega-modal--fullscreen .omega-modal__panel) {
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

