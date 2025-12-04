import { apiGet } from '../http'
import { ApiRoutes } from '../routes'
import type {
  InitData,
  Produto,
  ProdutoFilters,
  ProdutoMensal,
  DetalhesFilters,
  DetalhesItem,
  RankingFilters,
  RankingItem,
  ResumoPayload,
  Variavel,
  VariavelFilters,
  CalendarioItem,
  ExecData,
  ExecFilters
} from '../../types'

export type {
  InitData,
  Produto,
  ProdutoFilters,
  ProdutoMensal,
  DetalhesFilters,
  DetalhesItem,
  RankingFilters,
  RankingItem,
  ResumoPayload,
  Variavel,
  VariavelFilters,
  CalendarioItem,
  ExecData,
  ExecFilters
} from '../../types'

function buildFilterParams<T extends Record<string, string | undefined>>(
  filters?: T
): Record<string, string> {
  if (!filters) return {}
  const params: Record<string, string> = {}
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params[key] = value
  })
  return params
}

export async function getInit(): Promise<InitData | null> {
  const response = await apiGet<InitData>(ApiRoutes.INIT)
  return response.data ?? null
}

export async function getCalendario(): Promise<CalendarioItem[] | null> {
  const response = await apiGet<CalendarioItem[]>(ApiRoutes.CALENDARIO)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar calendário:', response.error)
  return null
}

export function getDefaultPeriod(): { start: string; end: string } {
  const today = new Date()
  const end = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const start = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate())

  const startISO = start.toISOString().split('T')[0] || ''
  const endISO = end.toISOString().split('T')[0] || ''

  return { start: startISO, end: endISO }
}

export function formatBRDate(dateString: string): string {
  if (!dateString) return ''
  const date = new Date(dateString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${day}/${month}/${year}`
}

function buildProdutoFilterParams(filters?: ProdutoFilters): Record<string, string> {
  if (!filters) return {}
  const params: Record<string, string> = {}
  const filterKeys: Array<keyof ProdutoFilters> = [
    'segmento',
    'diretoria',
    'regional',
    'agencia',
    'gerenteGestao',
    'gerente',
    'familia',
    'indicador',
    'subindicador',
    'dataInicio',
    'dataFim',
    'status'
  ]
  filterKeys.forEach((key) => {
    const value = filters[key]
    if (value) params[key] = value
  })
  return params
}

async function fetchProdutos<T>(route: string, filters?: ProdutoFilters): Promise<T[] | null> {
  const params = buildProdutoFilterParams(filters)
  const response = await apiGet<T[]>(route, params)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar produtos:', response.error)
  return null
}

export async function getProdutos(filters?: ProdutoFilters): Promise<Produto[] | null> {
  return fetchProdutos<Produto>(ApiRoutes.PRODUTOS, filters)
}

export async function getProdutosMensais(filters?: ProdutoFilters): Promise<ProdutoMensal[] | null> {
  return fetchProdutos<ProdutoMensal>('/api/produtos/mensais', filters)
}

export async function getDetalhes(filters?: DetalhesFilters): Promise<DetalhesItem[] | null> {
  const params = buildFilterParams(filters)
  const response = await apiGet<DetalhesItem[]>(ApiRoutes.DETALHES, params)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar detalhes:', response.error)
  return null
}

export async function getRanking(
  filters?: RankingFilters,
  nivel?: string
): Promise<RankingItem[] | null> {
  const params = buildFilterParams(filters)
  if (nivel) params.nivel = nivel
  const response = await apiGet<RankingItem[]>(ApiRoutes.RANKING, params)
  
  if (response.success && response.data) {
    return response.data
  }
  
  const errorMessage = response.error || 'Erro desconhecido ao buscar ranking'
  console.error('Erro ao buscar ranking:', errorMessage)
  
  if (typeof response.data === 'object' && response.data !== null && 'error' in response.data) {
    const backendError = (response.data as { error?: string }).error
    if (backendError?.includes('Column not found') || backendError?.includes('Unknown column')) {
      console.error('Erro SQL no backend:', backendError)
    }
  }
  
  return null
}

export async function getResumo(filters?: ProdutoFilters): Promise<ResumoPayload | null> {
  const params = buildProdutoFilterParams(filters)
  const response = await apiGet<ResumoPayload>(ApiRoutes.RESUMO, params)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar resumo:', response.error)
  return null
}

export async function getVariavel(filters?: VariavelFilters): Promise<Variavel[] | null> {
  const params = buildFilterParams(filters)
  const response = await apiGet<Variavel[]>(ApiRoutes.VARIAVEL, params)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar variável:', response.error)
  return null
}

export async function getExecData(filters?: ExecFilters): Promise<ExecData | null> {
  const params = buildFilterParams(filters)
  const response = await apiGet<ExecData>(ApiRoutes.EXEC, params)
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar dados executivos:', response.error)
  return null
}
