import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'

export interface RankingItem {
  data: string
  competencia: string
  segmento?: string
  segmento_id?: string
  diretoria_id?: string
  diretoria_nome?: string
  gerencia_id?: string
  gerencia_nome?: string
  agencia_id?: string
  agencia_nome?: string
  gerente_gestao_id?: string
  gerente_gestao_nome?: string
  gerente_id?: string
  gerente_nome?: string
  participantes?: number
  rank: number
  pontos?: number
  realizado_mensal?: number
  meta_mensal?: number
}

export interface RankingFilters {
  gerenteGestao?: string
}

export async function getRanking(filters?: RankingFilters): Promise<RankingItem[] | null> {
  const params: Record<string, string> = {}
  
  if (filters && filters.gerenteGestao) {
    params.gerenteGestao = filters.gerenteGestao
  }
  
  const response = await apiGet<RankingItem[]>(ApiRoutes.RANKING, params)

  if (response.success && response.data) {
    return response.data
  }

  console.error('Erro ao buscar ranking:', response.error)
  return null
}

