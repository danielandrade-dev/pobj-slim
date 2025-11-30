<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useOmega } from '../composables/useOmega'
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
const isOpen = ref(false)
const modalRoot = ref<HTMLElement | null>(null)
const omegaTemplateHtml = omegaTemplate

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
  try {
    const data = await omega.loadInit()
    console.log('✅ Dados do Omega carregados:', data)
    if (!data) {
      console.warn('⚠️ Nenhum dado retornado do Omega')
    }
  } catch (err) {
    console.error('❌ Erro ao carregar dados do Omega:', err)
  }
}

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
    // Depois carrega os dados
    loadOmegaData()
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
</style>

