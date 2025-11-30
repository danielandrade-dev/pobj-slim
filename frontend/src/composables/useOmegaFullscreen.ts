import { onBeforeUnmount } from 'vue'

let fullscreenKeydownHandler: ((e: KeyboardEvent) => void) | null = null

export function useOmegaFullscreen() {
  function setOmegaFullscreen(on?: boolean) {
    const root = document.getElementById('omega-modal')
    if (!root) {
      console.warn('⚠️ Modal não encontrado para fullscreen')
      return
    }

    const btn = root.querySelector('[data-omega-fullscreen-toggle]') as HTMLElement
    if (!btn) {
      console.warn('⚠️ Botão de fullscreen não encontrado')
      // Tenta encontrar pelo ID também
      const btnById = root.querySelector('#omega-fullscreen') as HTMLElement
      if (btnById) {
        console.log('✅ Botão encontrado pelo ID')
      }
    }

    const next = (typeof on === 'boolean') ? on : !root.classList.contains('omega-modal--fullscreen')

    console.log(`🖥️ Alternando fullscreen: ${next ? 'ativar' : 'desativar'}`)
    console.log('📋 Classes antes:', root.className)

    root.classList.toggle('omega-modal--fullscreen', next)

    console.log('📋 Classes depois:', root.className)
    console.log('📋 Tem classe fullscreen?', root.classList.contains('omega-modal--fullscreen'))

    if (btn) {
      btn.setAttribute('aria-pressed', next ? 'true' : 'false')
      btn.setAttribute('aria-label', next ? 'Sair de tela cheia' : 'Entrar em tela cheia')
      const icon = btn.querySelector('i')
      if (icon) icon.className = next ? 'ti ti-arrows-minimize' : 'ti ti-arrows-maximize'
      console.log('✅ Botão atualizado')
    }

    console.log(`🖥️ Tela cheia ${next ? 'ativada' : 'desativada'}`)
  }

  function bindOmegaFullscreenControls(root: HTMLElement) {
    console.log('🔧 Configurando controles de fullscreen...')

    // Remove listener anterior se existir
    if (fullscreenKeydownHandler) {
      document.removeEventListener('keydown', fullscreenKeydownHandler)
      fullscreenKeydownHandler = null
    }

    // Tenta encontrar o botão pelo atributo data ou pelo ID
    const fsBtn = root.querySelector('[data-omega-fullscreen-toggle]') as HTMLElement ||
                  root.querySelector('#omega-fullscreen') as HTMLElement

    if (fsBtn) {
      console.log('✅ Botão de fullscreen encontrado:', fsBtn)
      fsBtn.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        console.log('🖱️ Clique no botão de fullscreen detectado')
        setOmegaFullscreen()
      })
    } else {
      console.warn('⚠️ Botão de fullscreen não encontrado!')
      console.log('🔍 Procurando por:', {
        dataAttr: root.querySelector('[data-omega-fullscreen-toggle]'),
        id: root.querySelector('#omega-fullscreen'),
        allButtons: root.querySelectorAll('button')
      })
    }

    const header = root.querySelector('.omega-header')
    if (header) {
      header.addEventListener('dblclick', () => {
        console.log('🖱️ Duplo clique no header detectado')
        setOmegaFullscreen()
      })
    }

    fullscreenKeydownHandler = (ev: KeyboardEvent) => {
      // Só reage se o popup estiver visível
      if (!root || root.hidden) return
      if ((ev.key || '').toLowerCase() === 'f') {
        setOmegaFullscreen()
        ev.preventDefault()
      }
      // ESC sai do fullscreen antes de fechar o modal
      if (ev.key === 'Escape' && root.classList.contains('omega-modal--fullscreen')) {
        setOmegaFullscreen(false)
        ev.stopPropagation()
      }
    }

    document.addEventListener('keydown', fullscreenKeydownHandler, { passive: false })
  }

  function cleanup() {
    if (fullscreenKeydownHandler) {
      document.removeEventListener('keydown', fullscreenKeydownHandler)
      fullscreenKeydownHandler = null
    }
  }

  onBeforeUnmount(() => {
    cleanup()
  })

  return {
    setOmegaFullscreen,
    bindOmegaFullscreenControls,
    cleanup
  }
}

