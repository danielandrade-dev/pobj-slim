/**
 * Tipos e interfaces compartilhadas do projeto
 * Centraliza todas as definições de tipos para facilitar manutenção
 */

// ============================================================================
// FILTROS
// ============================================================================

/**
 * Opção de filtro genérica
 */
export interface FilterOption {
  id: string
  label: string
  nome?: string
  // Campos de relacionamento hierárquico organizacional
  id_segmento?: string
  id_diretoria?: string
  id_regional?: string
  id_agencia?: string
  id_gestor?: string
  funcional?: string
  id_original?: string // ID numérico original (para comparações hierárquicas)
  // Campos de relacionamento hierárquico de produtos
  id_familia?: string
  id_indicador?: string
}

/**
 * Seleção hierárquica de filtros organizacionais
 */
export interface HierarchySelection {
  segmento: string
  diretoria: string
  gerencia: string
  agencia: string
  ggestao: string
  gerente: string
}

// ============================================================================
// ESTRUTURA ORGANIZACIONAL
// ============================================================================

/**
 * Dados completos da estrutura organizacional retornados pela API
 */
export interface EstruturaData {
  segmentos: any[]
  diretorias: any[]
  regionais: any[]
  agencias: any[]
  gerentes_gestao: any[]
  gerentes: any[]
  familias: any[]
  indicadores: any[]
  subindicadores: any[]
  status_indicadores: any[]
}

/**
 * Item de segmento
 */
export interface SegmentoItem {
  id: string | number
  label: string
  nome?: string
}

// ============================================================================
// PERÍODO E CALENDÁRIO
// ============================================================================

/**
 * Período de datas (início e fim)
 */
export interface Period {
  start: string
  end: string
}

/**
 * Item de calendário retornado pela API
 */
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

// ============================================================================
// API
// ============================================================================

/**
 * Resposta padrão da API
 */
export interface ApiResponse<T> {
  success: boolean
  data?: T
  error?: string
  message?: string
}

// ============================================================================
// COMPONENTES
// ============================================================================

/**
 * Props do componente Button
 */
export interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'info' | 'link'
  icon?: string
  label?: string
  disabled?: boolean
}

// ============================================================================
// VIEWS E NAVEGAÇÃO
// ============================================================================

/**
 * View disponível no sistema
 */
export type ViewType = 'cards' | 'table' | 'ranking' | 'exec' | 'simuladores' | 'campanhas'

/**
 * Configuração de uma aba de navegação
 */
export interface TabConfig {
  id: ViewType
  label: string
  icon: string
  ariaLabel: string
}

