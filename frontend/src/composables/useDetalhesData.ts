import { ref, computed, watch, type Ref, type ComputedRef } from 'vue'
import type { Period, DetalhesItem, DetalhesFilters } from '../types'
import type { FilterState } from './useGlobalFilters'
import { getDetalhes } from '../api/modules/pobj.api'
import { buildDetalhesFilters, filtersEqual } from '../utils/filterUtils'
import { useGlobalFilters } from './useGlobalFilters'
import { useToast } from './useToast'

const detalhesPayload = ref<DetalhesItem[] | null>(null)
const detalhesLoading = ref(false)
const detalhesError = ref<string | null>(null)
const lastFilters = ref<DetalhesFilters | null>(null)

let watcherRegistered = false

async function fetchDetalhes(filters: DetalhesFilters): Promise<void> {
  if (lastFilters.value && filtersEqual(lastFilters.value, filters)) {
    return
  }
  
  if (detalhesLoading.value) {
    return
  }
  
  const { loading: showLoading, dismiss: dismissLoading, error: showError } = useToast()
  const loadingId = showLoading('Carregando detalhes...')
  
  lastFilters.value = filters
  detalhesLoading.value = true
  detalhesError.value = null

  try {
    const data = await getDetalhes(filters)
    dismissLoading(loadingId)
    if (data) {
      detalhesPayload.value = data
      if (data.length > 0) {
        showLoading(`Carregados ${data.length} registros`, 'success', 2000)
      }
    } else {
      detalhesError.value = 'Não foi possível carregar os dados de detalhes'
      detalhesPayload.value = []
      showError('Não foi possível carregar os dados de detalhes')
    }
  } catch (error) {
    dismissLoading(loadingId)
    const errorMsg = error instanceof Error ? error.message : 'Erro desconhecido'
    detalhesError.value = errorMsg
    showError(`Erro ao carregar detalhes: ${errorMsg}`)
  } finally {
    detalhesLoading.value = false
  }
}

export function useDetalhesData(
  filterState: Ref<FilterState> | ComputedRef<FilterState>,
  period: Ref<Period> | ComputedRef<Period>
) {
  if (!watcherRegistered) {
    watcherRegistered = true
    const { filterTrigger } = useGlobalFilters()
    
    watch(
      filterTrigger,
      () => {
        const filters = buildDetalhesFilters(filterState.value, period.value)
        fetchDetalhes(filters)
      },
      { immediate: true }
    )
  }

  const loadDetalhes = async () => {
    const filters = lastFilters.value ?? buildDetalhesFilters(filterState.value, period.value)
    await fetchDetalhes(filters)
  }

  return {
    detalhes: computed(() => detalhesPayload.value ?? []),
    loading: computed(() => detalhesLoading.value),
    error: computed(() => detalhesError.value),
    loadDetalhes,
    buildFilters: () => buildDetalhesFilters(filterState.value, period.value)
  }
}

