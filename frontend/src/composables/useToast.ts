import { ref } from 'vue'

export interface ToastMessage {
  id: string
  type: 'success' | 'error' | 'info' | 'loading'
  message: string
  duration?: number
}

const toasts = ref<ToastMessage[]>([])

export function useToast() {
  function show(message: string, type: ToastMessage['type'] = 'info', duration = 3000): void {
    const id = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
    const toast: ToastMessage = {
      id,
      type,
      message,
      duration: type === 'loading' ? 0 : duration
    }

    toasts.value.push(toast)

    if (duration > 0 && type !== 'loading') {
      setTimeout(() => {
        remove(id)
      }, duration)
    }
  }

  function remove(id: string): void {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  function success(message: string, duration = 3000): void {
    show(message, 'success', duration)
  }

  function error(message: string, duration = 5000): void {
    show(message, 'error', duration)
  }

  function info(message: string, duration = 3000): void {
    show(message, 'info', duration)
  }

  function loading(message: string): string {
    show(message, 'loading', 0)
    return toasts.value[toasts.value.length - 1]?.id || ''
  }

  function dismiss(id: string): void {
    remove(id)
  }

  return {
    toasts,
    show,
    success,
    error,
    info,
    loading,
    dismiss,
    remove
  }
}
