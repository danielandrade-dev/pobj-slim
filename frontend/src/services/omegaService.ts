import { apiGet, apiPost } from './api'
import type { ApiResponse } from '../types'
import type {
  OmegaInitData,
  OmegaUser,
  OmegaTicket,
  OmegaStatus,
  OmegaStructure,
  OmegaMesuRecord
} from '../types/omega'

export async function getOmegaInit(): Promise<OmegaInitData | null> {
  console.log('📡 Chamando API: /api/omega/init')
  const response = await apiGet<OmegaInitData>('/api/omega/init')
  console.log('📥 Resposta da API /api/omega/init:', response)
  if (response.success && response.data) {
    return response.data
  }
  console.error('❌ Erro ao buscar dados de inicialização do Omega:', response.error)
  return null
}

export async function getOmegaUsers(): Promise<OmegaUser[] | null> {
  const response = await apiGet<OmegaUser[]>('/api/omega/users')
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar usuários do Omega:', response.error)
  return null
}

export async function getOmegaTickets(): Promise<OmegaTicket[] | null> {
  const response = await apiGet<OmegaTicket[]>('/api/omega/tickets')
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar tickets do Omega:', response.error)
  return null
}

export async function getOmegaStatuses(): Promise<OmegaStatus[] | null> {
  const response = await apiGet<OmegaStatus[]>('/api/omega/statuses')
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar status do Omega:', response.error)
  return null
}

export async function getOmegaStructure(): Promise<OmegaStructure[] | null> {
  const response = await apiGet<OmegaStructure[]>('/api/omega/structure')
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar estrutura do Omega:', response.error)
  return null
}

export async function getOmegaMesu(): Promise<OmegaMesuRecord[] | null> {
  const response = await apiGet<OmegaMesuRecord[]>('/api/omega/mesu')
  if (response.success && response.data) {
    return response.data
  }
  console.error('Erro ao buscar MESU do Omega:', response.error)
  return null
}

export async function createOmegaTicket(
  ticket: Partial<OmegaTicket>
): Promise<ApiResponse<OmegaTicket | OmegaTicket[]>> {
  return apiPost<OmegaTicket | OmegaTicket[]>('/api/omega/tickets', ticket)
}

export async function updateOmegaTicket(
  ticketId: string,
  updates: Partial<OmegaTicket>
): Promise<ApiResponse<OmegaTicket>> {
  return apiPost<OmegaTicket>(`/api/omega/tickets/${ticketId}`, updates)
}

