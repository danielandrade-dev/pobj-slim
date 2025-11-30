export function getApiBaseUrl(): string {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  if (typeof window !== 'undefined' && (window as any).API_HTTP_BASE) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const base = String((window as any).API_HTTP_BASE).trim()
    if (base) {
      return base.endsWith('/') ? base.slice(0, -1) : base
    }
  }

  const envUrl = import.meta.env.VITE_API_URL
  if (envUrl) {
    return envUrl.endsWith('/') ? envUrl.slice(0, -1) : envUrl
  }

  if (import.meta.env.DEV) {
    return window.location.origin
  }

  if (typeof window !== 'undefined') {
    const basePath = import.meta.env.BASE_URL || '/'
    const origin = window.location.origin
    const base = basePath === '/' ? origin : `${origin}${basePath.replace(/\/$/, '')}`
    return base
  }

  return 'http://localhost:8081'
}

export const API_BASE_URL = getApiBaseUrl()

