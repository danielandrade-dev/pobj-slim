import { ref, readonly } from 'vue'
import { getInit, type InitData } from '../services/initService'

const initCache = ref<InitData | null>(null)
const isLoading = ref(false)
let loadPromise: Promise<InitData | null> | null = null

export function useInitCache() {
  const loadInit = async (): Promise<InitData | null> => {
    // Já temos no cache
    if (initCache.value) return initCache.value

    // Já existe requisição em andamento
    if (loadPromise) return loadPromise

    isLoading.value = true

    // Garante requisição única
    loadPromise = getInit()
      .then((data) => {
        if (data) initCache.value = data
        return data
      })
      .finally(() => {
        isLoading.value = false
        loadPromise = null
      })

    return loadPromise
  }

  const clearCache = () => {
    initCache.value = null
    loadPromise = null
  }

  return {
    initData: readonly(initCache),
    isLoading: readonly(isLoading),
    loadInit,
    clearCache
  }
}
