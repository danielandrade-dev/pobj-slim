import { API_BASE_URL } from '../config/api'
import type { ApiResponse } from '../types'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export async function apiGet<T = any>(
  path: string,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  params?: Record<string, any>
): Promise<ApiResponse<T>> {
  try {
    const cleanPath = path.startsWith('/') ? path : `/${path}`

    const baseUrl = API_BASE_URL
    const fullUrl = baseUrl.endsWith('/') 
      ? `${baseUrl}${cleanPath.startsWith('/') ? cleanPath.slice(1) : cleanPath}`
      : `${baseUrl}${cleanPath}`
    const url = new URL(fullUrl)

    if (params) {
      Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          url.searchParams.append(key, String(value))
        }
      })
    }

    const response = await fetch(url.toString(), {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    if (data.success !== undefined) {
      return data
    }

    return {
      success: true,
      data: data
    }
  } catch (error) {
    console.error('API Error:', error)
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error'
    }
  }
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export async function apiPost<T = any>(
  path: string,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  body?: Record<string, any>,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  params?: Record<string, any>
): Promise<ApiResponse<T>> {
  try {
    const cleanPath = path.startsWith('/') ? path : `/${path}`

    const baseUrl = API_BASE_URL
    const fullUrl = baseUrl.endsWith('/') 
      ? `${baseUrl}${cleanPath.startsWith('/') ? cleanPath.slice(1) : cleanPath}`
      : `${baseUrl}${cleanPath}`
    const url = new URL(fullUrl)

    if (params) {
      Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          url.searchParams.append(key, String(value))
        }
      })
    }

    const response = await fetch(url.toString(), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: body ? JSON.stringify(body) : undefined,
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    if (data.success !== undefined) {
      return data
    }

    return {
      success: true,
      data: data
    }
  } catch (error) {
    console.error('API Error:', error)
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error'
    }
  }
}

