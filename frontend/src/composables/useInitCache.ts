import { ref } from 'vue'
import { getInit, type InitData } from '../services/initService'

const initCache = ref<InitData | null>(null)
const isLoading = ref(false)
const loadPromise = ref<Promise<InitData | null> | null>(null)

export function useInitCache() {
  const loadInit = async (): Promise<InitData | null> => {
    if (initCache.value) {
      return initCache.value
    }

    if (loadPromise.value) {
      return loadPromise.value
    }

    isLoading.value = true
    loadPromise.value = getInit()
      .then((data) => {
        if (data) {
          initCache.value = data
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
    initCache.value = null
    loadPromise.value = null
  }

  return {
    initData: initCache,
    isLoading,
    loadInit,
    clearCache
  }
}


