import { apiGet } from '../http'
import { ApiRoutes } from '../routes'

export interface SimuladorProduct {
  id: string
  label: string
  sectionId: string
  sectionLabel: string
  metric: 'qtd' | 'valor'
  meta: number
  realizado: number
  variavelMeta: number
  variavelReal: number
  pontosMeta: number
  pontosBrutos: number
  pontos: number
  ultimaAtualizacao: string | null
}

export interface SimuladorFilters {
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerenteGestao?: string
  gerente?: string
  dataInicio?: string
  dataFim?: string
}

function buildFilterParams(filters?: SimuladorFilters): Record<string, string> {
  if (!filters) return {}
  const params: Record<string, string> = {}
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params[key] = value
  })
  return params
}

export async function getSimuladorProducts(
  filters?: SimuladorFilters
): Promise<SimuladorProduct[] | null> {
  const params = buildFilterParams(filters)
  const response = await apiGet<SimuladorProduct[]>(ApiRoutes.SIMULADOR, params)

  if (response.success && response.data) {
    return response.data
  }

  console.error('Erro ao buscar produtos para simulador:', response.error)
  return null
}
