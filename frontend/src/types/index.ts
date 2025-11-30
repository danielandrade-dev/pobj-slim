export interface FilterOption {
  id: string
  nome: string
  id_segmento?: string
  id_diretoria?: string
  id_regional?: string
  id_agencia?: string
  id_gestor?: string
  funcional?: string
  id_familia?: string
  id_indicador?: string
}

export interface HierarchySelection {
  segmento: string
  diretoria: string
  gerencia: string
  agencia: string
  ggestao: string
  gerente: string
}

export interface InitData {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  segmentos: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  diretorias: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  regionais: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  agencias: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  gerentes_gestao: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  gerentes: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  familias: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  indicadores: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  subindicadores: any[]
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  status_indicadores: any[]
}

export interface SegmentoItem {
  id: string | number
  nome: string
}

export interface Period {
  start: string
  end: string
}

export interface BusinessSnapshot {
  total: number
  elapsed: number
  remaining: number
  monthStart: string
  monthEnd: string
  today: string
}

export interface CalendarioItem {
  data: string
  competencia: string
  ano: string
  mes: string
  mesNome: string
  mesAnoCurto: string
  dia: string
  diaSemana: string
  semana: string
  trimestre: string
  semestre: string
  ehDiaUtil: number
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export interface ApiResponse<T = any> {
  success: boolean
  data?: T
  error?: string
  message?: string
}

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
  gerente_gestao_id?: string
  gerente_gestao_nome?: string
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
  gerenteGestao?: string
  familia?: string
  indicador?: string
  subindicador?: string
  dataInicio?: string
  dataFim?: string
}

export interface Produto {
  id: string
  id_familia: string
  familia: string
  id_indicador: string
  indicador: string
  id_subindicador?: string
  subindicador?: string
  metrica: string
  peso: number
  meta?: number
  realizado?: number
  pontos?: number
  pontos_meta?: number
  variavel_meta?: number
  variavel_realizado?: number
  ating?: number
  atingido?: boolean
  ultima_atualizacao?: string
}

export interface ProdutoFilters {
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerenteGestao?: string
  gerente?: string
  familia?: string
  indicador?: string
  subindicador?: string
  dataInicio?: string
  dataFim?: string
  status?: string
}

export interface ProdutoMensal {
  id: string
  id_indicador: string
  indicador: string
  id_familia: string
  familia: string
  id_subindicador?: string
  subindicador?: string
  metrica: string
  peso: number
  meta?: number
  realizado?: number
  pontos?: number
  pontos_meta?: number
  ating?: number
  atingido?: boolean
  ultima_atualizacao?: string
  meses: Array<{
    mes: string
    meta: number
    realizado: number
    atingimento: number
  }>
}

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
  segmento?: string
  diretoria?: string
  regional?: string
  agencia?: string
  gerenteGestao?: string
  gerente?: string
  dataInicio?: string
  dataFim?: string
}

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

export interface ResumoPayload {
  cards: Produto[]
  classifiedCards: ProdutoMensal[]
  variableCard: Variavel[]
  businessSnapshot: BusinessSnapshot
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

export interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'info' | 'link'
  icon?: string
  label?: string
  disabled?: boolean
}

export type ViewType = 'cards' | 'table' | 'ranking' | 'exec' | 'simuladores' | 'campanhas'

export interface TabConfig {
  id: ViewType
  label: string
  icon: string
  ariaLabel: string
  path?: string
}

