import { ref } from 'vue'
import { getCalendario, type CalendarioItem } from '../services/calendarioService'

const calendarioData = ref<CalendarioItem[]>([])
const isLoading = ref(false)
let loadPromise: Promise<CalendarioItem[] | null> | null = null

export function useCalendarioCache() {
  const loadCalendario = async (): Promise<CalendarioItem[] | null> => {
    // Já existe requisição em andamento, aguarda ela
    if (loadPromise) {
      return loadPromise
    }

    isLoading.value = true
    // Sempre busca dados frescos do servidor
    loadPromise = getCalendario()
      .then((data) => {
        if (data) {
          calendarioData.value = data
        }
        return data
      })
      .finally(() => {
        isLoading.value = false
        loadPromise = null
      })

    return loadPromise
  }

  return {
    calendarioData,
    isLoading,
    loadCalendario
  }
}


