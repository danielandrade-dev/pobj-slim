import { ref } from 'vue'
import { getCalendario, type CalendarioItem } from '../services/calendarioService'

const calendarioCache = ref<CalendarioItem[]>([])
const isLoading = ref(false)
const loadPromise = ref<Promise<CalendarioItem[] | null> | null>(null)

export function useCalendarioCache() {
  const loadCalendario = async (): Promise<CalendarioItem[] | null> => {
    if (calendarioCache.value.length > 0) {
      return calendarioCache.value
    }

    if (loadPromise.value) {
      return loadPromise.value
    }

    isLoading.value = true
    loadPromise.value = getCalendario()
      .then((data) => {
        if (data) {
          calendarioCache.value = data
        }
        return data
      })
      .finally(() => {
        isLoading.value = false
        loadPromise.value = null
      })

    return loadPromise.value
  }

  const clearCache = (): void => {
    calendarioCache.value = []
    loadPromise.value = null
  }

  return {
    calendarioData: calendarioCache,
    isLoading,
    loadCalendario,
    clearCache
  }
}


