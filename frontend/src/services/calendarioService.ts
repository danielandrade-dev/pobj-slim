import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'
import type { CalendarioItem } from '../types'

export type { CalendarioItem } from '../types'

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

  return {
    start: startISO,
    end: endISO
  }
}

export function formatBRDate(dateString: string): string {
  if (!dateString) return ''
  // Parse manual para evitar problemas de timezone
  // Formato esperado: YYYY-MM-DD
  const parts = dateString.split('-')
  if (parts.length !== 3) {
    // Fallback para o método antigo se não for formato ISO
    const date = new Date(dateString)
    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()
    return `${day}/${month}/${year}`
  }
  const year = parseInt(parts[0], 10)
  const month = parseInt(parts[1], 10)
  const day = parseInt(parts[2], 10)
  return `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`
}

