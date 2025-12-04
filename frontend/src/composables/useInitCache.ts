import { ref, readonly } from 'vue'
import { getInit, type InitData } from '../services/initService'

const initData = ref<InitData | null>(null)
const isLoading = ref(false)
let loadPromise: Promise<InitData | null> | null = null

export function useInitCache() {
  const loadInit = async (): Promise<InitData | null> => {
    // Já existe requisição em andamento, aguarda ela
    if (loadPromise) return loadPromise

    isLoading.value = true

    // Sempre busca dados frescos do servidor
    loadPromise = getInit()
      .then((data) => {
        if (data) initData.value = data
        return data
      })
      .finally(() => {
        isLoading.value = false
        loadPromise = null
      })

    return loadPromise
  }

  return {
    initData: readonly(initData),
    isLoading: readonly(isLoading),
    loadInit
  }
}
