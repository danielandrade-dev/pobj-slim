import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'

export interface DetalhesItem {
  registro_id?: string
  id_contrato?: string
  data?: string
  competencia?: string
  ano?: number
  mes?: number
  mes_nome?: string
  segmento_id?: string
  segmento?: string
  diretoria_id?: string
  diretoria_nome?: string
  gerencia_id?: string
  gerencia_nome?: string
  agencia_id?: string
  agencia_nome?: string
  gerente_id?: string
  gerente_nome?: string
  familia_id?: string
  familia_nome?: string
  id_indicador?: string
  ds_indicador?: string
  id_subindicador?: string
  subindicador?: string
  peso?: number
  valor_realizado?: number
  valor_meta?: number
  meta_mensal?: number
  canal_venda?: string
  tipo_venda?: string
  modalidade_pagamento?: string
  dt_vencimento?: string
  dt_cancelamento?: string
  motivo_cancelamento?: string
  status_id?: number
}

export interface DetalhesFilters {
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerente?: string
  familia?: string
  indicador?: string
  subindicador?: string
  dataInicio?: string
  dataFim?: string
}

export async function getDetalhes(filters?: DetalhesFilters): Promise<DetalhesItem[] | null> {
  const params: Record<string, string> = {}
  
  if (filters) {
    if (filters.segmento) params.segmento = filters.segmento
    if (filters.diretoria) params.diretoria = filters.diretoria
    if (filters.regional) params.regional = filters.regional
    if (filters.agencia) params.agencia = filters.agencia
    if (filters.gerente) params.gerente = filters.gerente
    if (filters.familia) params.familia = filters.familia
    if (filters.indicador) params.indicador = filters.indicador
    if (filters.subindicador) params.subindicador = filters.subindicador
    if (filters.dataInicio) params.dataInicio = filters.dataInicio
    if (filters.dataFim) params.dataFim = filters.dataFim
  }
  
  const response = await apiGet<DetalhesItem[]>(ApiRoutes.DETALHES, params)

  if (response.success && response.data) {
    return response.data
  }

  console.error('Erro ao buscar detalhes:', response.error)
  return null
}

