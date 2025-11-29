import { ref, computed, watch, type Ref, type ComputedRef } from 'vue'
import type { Period, ProdutoFilters, BusinessSnapshot, ResumoPayload } from '../types'
import type { FilterState } from './useGlobalFilters'
import { getResumo, type ResumoFilters } from '../services/resumoService'

interface WatchSources {
  filters: FilterState
  period: Period
}

const resumoPayload = ref<ResumoPayload | null>(null)
const resumoLoading = ref(false)
const resumoError = ref<string | null>(null)
const lastFilters = ref<ResumoFilters | null>(null)

let watcherRegistered = false

const emptySnapshot: BusinessSnapshot = {
  total: 0,
  elapsed: 0,
  remaining: 0,
  monthStart: '',
  monthEnd: '',
  today: ''
}

function sanitizeValue(value?: string | null): string | undefined {
  if (!value) return undefined
  const trimmed = value.trim()
  if (!trimmed) return undefined
  const lower = trimmed.toLowerCase()
  if (lower === 'todos' || lower === 'todas') return undefined
  return trimmed
}

function buildFiltersFromState(state?: FilterState, period?: Period): ResumoFilters {
  const filters: ResumoFilters = {}
  if (state) {
    const segmento = sanitizeValue(state.segmento)
    const diretoria = sanitizeValue(state.diretoria)
    const regional = sanitizeValue(state.gerencia)
    const agencia = sanitizeValue(state.agencia)
    const ggestao = sanitizeValue(state.ggestao)
    const gerente = sanitizeValue(state.gerente)
    const familia = sanitizeValue(state.familia)
    const indicador = sanitizeValue(state.indicador)
    const subindicador = sanitizeValue(state.subindicador)
    const status = state.status && state.status !== 'todos' ? state.status : undefined

    if (segmento) filters.segmento = segmento
    if (diretoria) filters.diretoria = diretoria
    if (regional) filters.regional = regional
    if (agencia) filters.agencia = agencia
    if (ggestao) filters.gerenteGestao = ggestao
    if (gerente) filters.gerente = gerente
    if (familia) filters.familia = familia
    if (indicador) filters.indicador = indicador
    if (subindicador) filters.subindicador = subindicador
    if (status) filters.status = status
  }

  if (period?.start) {
    filters.dataInicio = period.start
  }
  if (period?.end) {
    filters.dataFim = period.end
  }

  return filters
}

// Função para comparar dois objetos de filtros
function filtersEqual(f1: ResumoFilters, f2: ResumoFilters): boolean {
  const keys1 = Object.keys(f1).sort()
  const keys2 = Object.keys(f2).sort()
  
  if (keys1.length !== keys2.length) {
    return false
  }
  
  for (const key of keys1) {
    if (f1[key] !== f2[key]) {
      return false
    }
  }
  
  return true
}

async function fetchResumo(filters: ResumoFilters): Promise<void> {
  // Evita chamadas duplicadas com os mesmos filtros
  if (lastFilters.value && filtersEqual(lastFilters.value, filters)) {
    return
  }
  
  // Evita múltiplas chamadas simultâneas
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
    
    // Usa um timeout para evitar múltiplas chamadas no carregamento inicial
    let timeoutId: ReturnType<typeof setTimeout> | null = null
    
    watch(
      () => ({
        filters: filterState.value,
        period: period.value
      }),
      (current: WatchSources) => {
        // Debounce para evitar múltiplas chamadas rápidas
        if (timeoutId) {
          clearTimeout(timeoutId)
        }
        
        timeoutId = setTimeout(() => {
          const filters = buildFiltersFromState(current.filters, current.period)
          fetchResumo(filters)
          timeoutId = null
        }, 100) // 100ms de debounce
      },
      { deep: true, immediate: true }
    )
  }

  const loadResumo = async () => {
    const filters = lastFilters.value ?? buildFiltersFromState(filterState.value, period.value)
    await fetchResumo(filters)
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
    buildFilters: () => buildFiltersFromState(filterState.value, period.value)
  }
}

