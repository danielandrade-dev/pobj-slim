import { ref, computed, watch, nextTick, type Ref, type ComputedRef } from 'vue'
import type { Period, BusinessSnapshot, ResumoPayload, ProdutoFilters } from '../types'
import type { FilterState } from './useGlobalFilters'
import { getResumo } from '../api/modules/pobj.api'
import { buildProdutoFilters, filtersEqual } from '../utils/filterUtils'
import { useGlobalFilters } from './useGlobalFilters'

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
  // Se não for forçado e os filtros são iguais, não busca novamente
  if (!force && lastFilters.value && filtersEqual(lastFilters.value, filters)) {
    return
  }
  
  if (resumoLoading.value) {
    return
  }
  
  lastFilters.value = filters
  resumoLoading.value = true
  resumoError.value = null

  try {
    const data = await getResumo(filters)
    if (data) {
      resumoPayload.value = data
    } else {
      resumoError.value = 'Não foi possível carregar o resumo'
    }
  } catch (error) {
    console.error('Erro ao carregar resumo:', error)
    resumoError.value = error instanceof Error ? error.message : 'Erro desconhecido'
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
    
    // Observa o filterTrigger para forçar busca quando o botão Filtrar é clicado
    // Este é o watcher principal que dispara a busca
    watch(
      filterTrigger,
      async () => {
        // Aguarda o próximo tick para garantir que as atualizações reativas foram aplicadas
        await nextTick()
        // Força a busca mesmo se os filtros forem iguais (quando o usuário clica em Filtrar)
        // Usa os valores globais para garantir que está lendo o estado mais recente
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

