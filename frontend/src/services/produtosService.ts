import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'
import type { Produto, ProdutoFilters, ProdutoMensal } from '../types'

export type { Produto, ProdutoFilters, ProdutoMensal } from '../types'

function buildFilterParams(filters?: ProdutoFilters): Record<string, string> {
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
  const params = buildFilterParams(filters)
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

