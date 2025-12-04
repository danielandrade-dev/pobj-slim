import { apiGet } from './api'
import { ApiRoutes } from '../constants/apiRoutes'
import type { InitData } from '../types'

export type { InitData } from '../types'

export async function getInit(): Promise<InitData | null> {
  const response = await apiGet<InitData>(ApiRoutes.INIT)
  return response.data ?? null
}
