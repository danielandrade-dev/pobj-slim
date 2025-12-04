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
