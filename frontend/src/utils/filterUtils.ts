import type { FilterState } from '../composables/useGlobalFilters'
import type { Period, ProdutoFilters, DetalhesFilters } from '../types'

export function sanitizeValue(value?: string | null): string | undefined {
  if (!value) return undefined
  const trimmed = value.trim()
  if (!trimmed) return undefined
  const lower = trimmed.toLowerCase()
  if (lower === 'todos' || lower === 'todas') return undefined
  return trimmed
}

export function mapStatusToBackend(status?: string): string | undefined {
  if (!status || status === 'todos') return undefined
  if (status === 'atingidos') return '01'
  if (status === 'nao') return '02'
  return undefined
}

interface BaseFilterFields {
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerenteGestao?: string
  gerente?: string
  familia?: string
  indicador?: string
  subindicador?: string
}

function buildBaseFilters(state?: FilterState): BaseFilterFields {
  if (!state) return {}

  return {
    segmento: sanitizeValue(state.segmento),
    diretoria: sanitizeValue(state.diretoria),
    regional: sanitizeValue(state.gerencia),
    agencia: sanitizeValue(state.agencia),
    gerenteGestao: sanitizeValue(state.ggestao),
    gerente: sanitizeValue(state.gerente),
    familia: sanitizeValue(state.familia),
    indicador: sanitizeValue(state.indicador),
    subindicador: sanitizeValue(state.subindicador)
  }
}

function addPeriodToFilters<T extends { dataInicio?: string; dataFim?: string }>(
  filters: T,
  period?: Period
): T {
  if (period?.start) {
    filters.dataInicio = period.start
  }
  if (period?.end) {
    filters.dataFim = period.end
  }
  return filters
}

export function buildProdutoFilters(
  state?: FilterState,
  period?: Period
): ProdutoFilters {
  const base = buildBaseFilters(state)
  const filters: ProdutoFilters = {}

  if (base.segmento) filters.segmento = base.segmento
  if (base.diretoria) filters.diretoria = base.diretoria
  if (base.regional) filters.regional = base.regional
  if (base.agencia) filters.agencia = base.agencia
  if (base.gerenteGestao) filters.gerenteGestao = base.gerenteGestao
  if (base.gerente) filters.gerente = base.gerente
  if (base.familia) filters.familia = base.familia
  if (base.indicador) filters.indicador = base.indicador
  if (base.subindicador) filters.subindicador = base.subindicador

  const status = mapStatusToBackend(state?.status)
  if (status) filters.status = status

  return addPeriodToFilters(filters, period)
}

export function buildDetalhesFilters(
  state?: FilterState,
  period?: Period
): DetalhesFilters {
  const base = buildBaseFilters(state)
  const filters: DetalhesFilters = {}

  if (base.segmento) filters.segmento = base.segmento
  if (base.diretoria) filters.diretoria = base.diretoria
  if (base.regional) filters.regional = base.regional
  if (base.agencia) filters.agencia = base.agencia
  if (base.gerente) filters.gerente = base.gerente
  if (base.gerenteGestao) filters.gerenteGestao = base.gerenteGestao
  if (base.familia) filters.familia = base.familia
  if (base.indicador) filters.indicador = base.indicador
  if (base.subindicador) filters.subindicador = base.subindicador

  return addPeriodToFilters(filters, period)
}

export function filtersEqual<T extends Record<string, unknown>>(
  f1: T,
  f2: T
): boolean {
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
