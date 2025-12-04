import { apiPost } from '../http'
import { ApiRoutes } from '../routes'

export interface AgentRequest {
  question: string
  user_name?: string
}

export interface AgentResponse {
  answer: string
  sources?: Array<{
    rank: number
    file: string
    path: string
    chunk: string
    score: number
  }>
  model?: string
}

export async function sendMessage(request: AgentRequest): Promise<AgentResponse | null> {
  const response = await apiPost<AgentResponse>(ApiRoutes.AGENT, request)

  if (response.success && response.data) {
    return response.data
  }

  console.error('Erro ao enviar mensagem para o agente:', response.error)
  return null
}
