import { ref, computed, watch, nextTick, type Ref, type ComputedRef } from 'vue'
import type { Period, BusinessSnapshot, ResumoPayload, ProdutoFilters } from '../types'
import type { FilterState } from './useGlobalFilters'
import { getResumo } from '../api/modules/pobj.api'
import { buildProdutoFilters, filtersEqual } from '../utils/filterUtils'
import { useGlobalFilters } from './useGlobalFilters'
import { useToast } from './useToast'

const resumoPayload = ref<ResumoPayload | null>(null)
const resumoLoading = ref(false)
const resumoError = ref<string | null>(null)
const lastFilters = ref<ProdutoFilters | null>(null)

let watcherRegistered = false

const emptySnapshot: BusinessSnapshot = {
  total: 0,
  elapsed: 0,
  remaining: 0,
  monthStart: '',
  monthEnd: '',
  today: ''
}

async function fetchResumo(filters: ProdutoFilters, force = false): Promise<void> {
  if (!force && lastFilters.value && filtersEqual(lastFilters.value, filters)) {
    return
  }
  
  if (resumoLoading.value) {
    return
  }
  
  const { loading: showLoading, dismiss: dismissLoading, error: showError } = useToast()
  const loadingId = showLoading('Carregando resumo...')
  
  lastFilters.value = filters
  resumoLoading.value = true
  resumoError.value = null

  try {
    const data = await getResumo(filters)
    dismissLoading(loadingId)
    if (data) {
      resumoPayload.value = data
      const totalCards = (data.cards?.length || 0) + (data.classifiedCards?.length || 0)
      if (totalCards > 0) {
        showLoading(`Resumo carregado com ${totalCards} itens`, 'success', 2000)
      }
    } else {
      resumoError.value = 'Não foi possível carregar o resumo'
      showError('Não foi possível carregar o resumo')
    }
  } catch (error) {
    dismissLoading(loadingId)
    const errorMsg = error instanceof Error ? error.message : 'Erro desconhecido'
    resumoError.value = errorMsg
    showError(`Erro ao carregar resumo: ${errorMsg}`)
  } finally {
    resumoLoading.value = false
  }
}

export function useResumoData(
  filterState: Ref<FilterState> | ComputedRef<FilterState>,
  period: Ref<Period> | ComputedRef<Period>
) {
  if (!watcherRegistered) {
    watcherRegistered = true
    const { filterTrigger, filterState: globalFilterState, period: globalPeriod } = useGlobalFilters()
    
    watch(
      filterTrigger,
      async () => {
        await nextTick()
        const filters = buildProdutoFilters(globalFilterState.value, globalPeriod.value)
        fetchResumo(filters, true)
      },
      { immediate: true }
    )
  }

  const loadResumo = async () => {
    const filters = lastFilters.value ?? buildProdutoFilters(filterState.value, period.value)
    await fetchResumo(filters, true)
  }

  return {
    resumo: computed(() => resumoPayload.value),
    produtos: computed(() => resumoPayload.value?.cards ?? []),
    produtosMensais: computed(() => resumoPayload.value?.classifiedCards ?? []),
    variavel: computed(() => resumoPayload.value?.variableCard ?? []),
    businessSnapshot: computed(() => resumoPayload.value?.businessSnapshot ?? emptySnapshot),
    loading: computed(() => resumoLoading.value),
    error: computed(() => resumoError.value),
    loadResumo,
    buildFilters: () => buildProdutoFilters(filterState.value, period.value)
  }
}

