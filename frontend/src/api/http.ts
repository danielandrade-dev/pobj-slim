import type { ApiResponse } from '../types'

function removeTrailingSlash(url: string): string {
  return url.replace(/\/$/, '')
}

function getWindowGlobal(key: string): string | undefined {
  if (typeof window === 'undefined') return undefined
  const value = (window as Record<string, unknown>)[key]
  return value ? String(value).trim() : undefined
}

export function getApiBaseUrl(): string {
  const globalBase = getWindowGlobal('API_HTTP_BASE')
  if (globalBase) return removeTrailingSlash(globalBase)

  const envUrl = import.meta.env.VITE_API_URL
  if (envUrl) return removeTrailingSlash(envUrl)

  if (import.meta.env.DEV && typeof window !== 'undefined') {
    const { hostname, protocol, origin } = window.location
    const port = import.meta.env.VITE_API_PORT
    const isIp = /^\d+\.\d+\.\d+\.\d+$/.test(hostname)

    if (isIp && port) {
      return removeTrailingSlash(`${protocol}//${hostname}:${port}`)
    }

    return removeTrailingSlash(origin)
  }

  if (typeof window !== 'undefined') {
    return removeTrailingSlash(window.location.origin)
  }

  return 'http://localhost:8081'
}

export function getApiKey(): string | null {
  const globalKey = getWindowGlobal('API_KEY')
  if (globalKey) return globalKey

  const envKey = import.meta.env.VITE_API_KEY
  return envKey ? String(envKey).trim() : null
}

export const API_BASE_URL = getApiBaseUrl()

function buildUrl(path: string, params?: Record<string, unknown>): string {
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

function buildHeaders(customHeaders?: Record<string, string>): Record<string, string> {
  const headers: Record<string, string> = { ...customHeaders }
  const apiKey = getApiKey()
  if (apiKey) {
    headers['X-API-Key'] = apiKey
  }
  return headers
}

async function handleResponse<T>(response: Response): Promise<ApiResponse<T>> {
  if (!response.ok) {
    const errorText = await response.text()
    let errorMessage = `HTTP ${response.status}`
    
    try {
      const errorData = JSON.parse(errorText)
      if (errorData?.data?.error) {
        errorMessage = errorData.data.error
      } else if (errorData?.error) {
        errorMessage = errorData.error
      } else if (errorData?.message) {
        errorMessage = errorData.message
      }
    } catch {
      if (errorText) {
        errorMessage = errorText.substring(0, 200)
      }
    }
    
    throw new Error(errorMessage)
  }

  const data = await response.json()
  
  if (data.success === false && data.data?.error) {
    return {
      success: false,
      error: data.data.error,
      data: data.data
    }
  }
  
  return data.success !== undefined ? data : { success: true, data }
}

async function request<T>(
  method: string,
  path: string,
  options?: {
    body?: Record<string, unknown>
    params?: Record<string, unknown>
    headers?: Record<string, string>
  }
): Promise<ApiResponse<T>> {
  try {
    const url = buildUrl(path, options?.params)
    const headers = buildHeaders({
      ...options?.headers,
      ...(options?.body && { 'Content-Type': 'application/json' })
    })

    const response = await fetch(url, {
      method,
      headers,
      body: options?.body ? JSON.stringify(options.body) : undefined
    })

    return await handleResponse<T>(response)
  } catch (error) {
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error'
    }
  }
}

export async function apiGet<T>(
  path: string,
  params?: Record<string, unknown>
): Promise<ApiResponse<T>> {
  return request<T>('GET', path, { params })
}

export async function apiPost<T>(
  path: string,
  body?: Record<string, unknown>,
  params?: Record<string, unknown>
): Promise<ApiResponse<T>> {
  return request<T>('POST', path, { body, params })
}

export async function apiPut<T>(
  path: string,
  body?: Record<string, unknown>,
  params?: Record<string, unknown>
): Promise<ApiResponse<T>> {
  return request<T>('PUT', path, { body, params })
}

export async function apiDelete<T>(
  path: string,
  params?: Record<string, unknown>
): Promise<ApiResponse<T>> {
  return request<T>('DELETE', path, { params })
}
