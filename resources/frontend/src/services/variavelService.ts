import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'

export interface Variavel {
  id?: string
  registro_id?: string
  funcional: string
  variavel_meta: number
  variavel_real: number
  dt_atualizacao?: string
  nome_funcional?: string
  segmento?: string
  segmento_id?: string
  diretoria_nome?: string
  diretoria_id?: string
  regional_nome?: string
  gerencia_id?: string
  agencia_nome?: string
  agencia_id?: string
  data?: string
  competencia?: string
}

export interface VariavelFilters {
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerenteGestao?: string
  gerente?: string
  dataInicio?: string
  dataFim?: string
}

export async function getVariavel(filters?: VariavelFilters): Promise<Variavel[] | null> {
  const params: Record<string, string> = {}
  
  if (filters) {
    if (filters.segmento) params.segmento = filters.segmento
    if (filters.diretoria) params.diretoria = filters.diretoria
    if (filters.regional) params.regional = filters.regional
    if (filters.agencia) params.agencia = filters.agencia
    if (filters.gerenteGestao) params.gerenteGestao = filters.gerenteGestao
    if (filters.gerente) params.gerente = filters.gerente
    if (filters.dataInicio) params.dataInicio = filters.dataInicio
    if (filters.dataFim) params.dataFim = filters.dataFim
  }
  
  const response = await apiGet<Variavel[]>(ApiRoutes.VARIAVEL, params)

  if (response.success && response.data) {
    return response.data
  }

  console.error('Erro ao buscar variável:', response.error)
  return null
}

