import { ref, computed, watch, type Ref, type ComputedRef } from 'vue'
import type { Period, DetalhesItem, DetalhesFilters } from '../types'
import type { FilterState } from './useGlobalFilters'
import { getDetalhes } from '../api/modules/pobj.api'
import { buildDetalhesFilters, filtersEqual } from '../utils/filterUtils'
import { useGlobalFilters } from './useGlobalFilters'

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
  
  lastFilters.value = filters
  detalhesLoading.value = true
  detalhesError.value = null

  try {
    const data = await getDetalhes(filters)
    if (data) {
      detalhesPayload.value = data
    } else {
      detalhesError.value = 'Não foi possível carregar os dados de detalhes'
      detalhesPayload.value = []
    }
  } catch (error) {
    console.error('Erro ao carregar detalhes:', error)
    detalhesError.value = error instanceof Error ? error.message : 'Erro desconhecido'
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

