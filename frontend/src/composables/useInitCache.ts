import { ref, readonly } from 'vue'
import { getInit, type InitData } from '../services/initService'

const initCache = ref<InitData | null>(null)
const isLoading = ref(false)
let loadPromise: Promise<InitData | null> | null = null

export function useInitCache() {
  async function loadInit(): Promise<InitData | null> {
    if (initCache.value) return initCache.value
    if (loadPromise) return loadPromise

    isLoading.value = true

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

  function clearCache(): void {
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
