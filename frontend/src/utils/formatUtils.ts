function parseNumber(value: number | string | null | undefined): number {
  if (value === null || value === undefined || value === '') return 0
  const num = typeof value === 'string' ? parseFloat(value) : value
  return Number.isFinite(num) ? num : 0
}

function formatCurrency(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

function formatInteger(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(Math.round(value))
}

function formatDecimal(value: number, decimals = 2): string {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  }).format(value)
}

export function formatBRL(value: number | string | null | undefined): string {
  const num = parseNumber(value)
  return formatCurrency(num)
}

export function formatPoints(value: number | string | null | undefined, options?: { withUnit?: boolean }): string {
  const num = parseNumber(value)
  const formatted = formatInteger(num)
  return options?.withUnit ? `${formatted} pts` : formatted
}

export function formatPeso(value: number | string | null | undefined): string {
  const num = parseNumber(value)
  return formatDecimal(num, 2)
}

const SUFFIX_RULES = [
  { value: 1_000_000_000_000, singular: 'trilhão', plural: 'trilhões' },
  { value: 1_000_000_000, singular: 'bilhão', plural: 'bilhões' },
  { value: 1_000_000, singular: 'milhão', plural: 'milhões' },
  { value: 1_000, singular: 'mil', plural: 'mil' }
]

const CURRENCY_SYMBOL = 'R$'
const CURRENCY_LITERAL = ' '

export function formatNumberWithSuffix(value: number | string | null | undefined, options: { currency?: boolean } = {}): string {
  const n = parseNumber(value)
  if (!Number.isFinite(n)) {
    return options.currency ? formatBRL(0) : '0'
  }
  
  const abs = Math.abs(n)
  
  if (abs < 1000) {
    return options.currency ? formatBRL(n) : formatInteger(n)
  }
  
  const rule = SUFFIX_RULES.find(r => abs >= r.value)
  if (!rule) {
    return options.currency ? formatBRL(n) : formatInteger(n)
  }
  
  const absScaled = abs / rule.value
  const nearInteger = Math.abs(absScaled - Math.round(absScaled)) < 0.05
  const digits = absScaled >= 100 ? 0 : nearInteger ? 0 : 1
  
  const formatted = new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: digits,
    maximumFractionDigits: digits
  }).format(absScaled)
  
  const isSingular = Math.abs(absScaled - 1) < 0.05
  const label = isSingular ? rule.singular : rule.plural
  const sign = n < 0 ? '-' : ''
  
  if (options.currency) {
    return `${sign}${CURRENCY_SYMBOL}${CURRENCY_LITERAL}${formatted} ${label}`
  }
  
  return `${sign}${formatted} ${label}`
}

function getMetricType(metric: string): 'currency' | 'quantity' | 'percent' {
  const metricLower = metric?.toLowerCase() || 'valor'
  
  if (['valor', 'brl', 'variavel'].includes(metricLower)) {
    return 'currency'
  }
  if (['perc', 'percentual', 'percent'].includes(metricLower)) {
    return 'percent'
  }
  return 'quantity'
}

export function formatByMetric(metric: string, value: number | string | null | undefined): string {
  const num = parseNumber(value)
  if (!Number.isFinite(num)) return 'N/A'

  const metricType = getMetricType(metric)
  
  if (metricType === 'currency') {
    return formatNumberWithSuffix(num, { currency: true })
  }
  if (metricType === 'percent') {
    return new Intl.NumberFormat('pt-BR', {
      style: 'percent',
      minimumFractionDigits: 1,
      maximumFractionDigits: 1
    }).format(num / 100)
  }
  return formatNumberWithSuffix(num, { currency: false })
}

export function formatMetricFull(metric: string, value: number | string | null | undefined): string {
  const num = parseNumber(value)
  if (!Number.isFinite(num)) return 'N/A'

  const metricType = getMetricType(metric)
  
  if (metricType === 'currency') {
    return formatCurrency(num)
  }
  if (metricType === 'percent') {
    return new Intl.NumberFormat('pt-BR', {
      style: 'percent',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(num / 100)
  }
  return formatDecimal(num, 2)
}

export function formatBRLReadable(value: number | string | null | undefined): string {
  return formatNumberWithSuffix(value, { currency: true })
}

export function formatIntReadable(value: number | string | null | undefined): string {
  return formatNumberWithSuffix(value, { currency: false })
}

export function formatINT(value: number | string | null | undefined): string {
  const num = parseNumber(value)
  return formatInteger(num)
}

export function formatCurrency(value: number | string | null | undefined): string {
  return formatBRL(value)
}

export function formatDate(isoDate: string | null | undefined): string {
  if (!isoDate) return '—'
  try {
    const [year, month, day] = isoDate.split('-')
    return `${day}/${month}/${year}`
  } catch {
    return isoDate
  }
}


