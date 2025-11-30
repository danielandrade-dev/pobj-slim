import { API_BASE_URL } from '../config/api'
import type { ApiResponse } from '../types'

function buildUrl(path: string, params?: Record<string, any>) {
  const cleanPath = path.startsWith('/') ? path : `/${path}`
  const base = API_BASE_URL.replace(/\/$/, '')
  const url = new URL(base + cleanPath)

  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        url.searchParams.append(key, String(value))
      }
    })
  }

  return url.toString()
}

export async function apiGet<T>(
  path: string,
  params?: Record<string, any>
): Promise<ApiResponse<T>> {
  try {
    const url = buildUrl(path, params)

    const response = await fetch(url, {
      method: 'GET'
      // sem Content-Type no GET
    })

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const data = await response.json()

    return data.success !== undefined
      ? data
      : { success: true, data }
  } catch (e) {
    return {
      success: false,
      error: e instanceof Error ? e.message : 'Unknown error'
    }
  }
}

export async function apiPost<T>(
  path: string,
  body?: Record<string, any>,
  params?: Record<string, any>
): Promise<ApiResponse<T>> {
  try {
    const url = buildUrl(path, params)

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: body ? JSON.stringify(body) : undefined
    })

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const data = await response.json()

    return data.success !== undefined
      ? data
      : { success: true, data }
  } catch (e) {
    return {
      success: false,
      error: e instanceof Error ? e.message : 'Unknown error'
    }
  }
}
