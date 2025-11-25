import { ref, computed } from 'vue'
import { getCalendario } from '../services/calendarioService'

// Função auxiliar para obter data de hoje no formato ISO (YYYY-MM-DD)
function todayISO(): string {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

// Função auxiliar para obter limites do mês (início e fim)
function getMonthBoundsForISO(isoDate: string): { start: string; end: string } {
  const date = new Date(isoDate)
  const year = date.getFullYear()
  const month = date.getMonth()
  
  const start = new Date(year, month, 1)
  const end = new Date(year, month + 1, 0)
  
  const startISO = `${year}-${String(month + 1).padStart(2, '0')}-01`
  const endISO = `${year}-${String(month + 1).padStart(2, '0')}-${String(end.getDate()).padStart(2, '0')}`
  
  return { start: startISO, end: endISO }
}

export interface BusinessSnapshot {
  total: number
  elapsed: number
  remaining: number
  monthStart: string
  monthEnd: string
}

export function useBusinessDays() {
  const calendario = ref<any[]>([])
  const loading = ref(false)

  const loadCalendario = async () => {
    try {
      loading.value = true
      const data = await getCalendario()
      if (data) {
        calendario.value = data
      }
    } catch (error) {
      console.error('Erro ao carregar calendário:', error)
    } finally {
      loading.value = false
    }
  }

  // Calcula dias úteis entre duas datas (fallback se não tiver calendário)
  const businessDaysBetweenInclusive = (start: string, end: string): number => {
    const startDate = new Date(start)
    const endDate = new Date(end)
    let count = 0
    const current = new Date(startDate)
    
    while (current <= endDate) {
      const dayOfWeek = current.getDay()
      if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Não é domingo (0) nem sábado (6)
        count++
      }
      current.setDate(current.getDate() + 1)
    }
    
    return count
  }

  // Obtém snapshot do mês atual com dias úteis
  const getCurrentMonthBusinessSnapshot = computed((): BusinessSnapshot => {
    const today = todayISO()
    const { start: monthStart, end: monthEnd } = getMonthBoundsForISO(today)
    const monthKey = today.slice(0, 7) // YYYY-MM
    
    let total = 0
    let elapsed = 0
    
    // Tenta usar o calendário se disponível
    if (calendario.value.length > 0) {
      const entries = calendario.value.filter(entry => {
        const data = entry.data || entry.dt || ''
        return typeof data === 'string' && data.startsWith(monthKey)
      })
      
      const businessEntries = entries.filter(entry => {
        const utilFlag = entry.ehDiaUtil ?? entry.util ?? entry.diaUtil ?? ''
        const value = typeof utilFlag === 'string' ? utilFlag.trim() : utilFlag
        if (value === true || value === 1 || value === '1') return true
        if (typeof value === 'string' && value.toLowerCase() === 'sim') return true
        return false
      })
      
      total = businessEntries.length
      const todayFiltered = businessEntries.filter(entry => (entry.data || entry.dt || '') <= today)
      elapsed = todayFiltered.length
    }
    
    // Fallback: calcula dias úteis manualmente
    if (!total) {
      total = businessDaysBetweenInclusive(monthStart, monthEnd)
      const cappedToday = today < monthStart ? monthStart : (today > monthEnd ? monthEnd : today)
      elapsed = businessDaysBetweenInclusive(monthStart, cappedToday)
    }
    
    const remaining = Math.max(0, total - elapsed)
    
    return { total, elapsed, remaining, monthStart, monthEnd }
  })

  return {
    calendario,
    loading,
    loadCalendario,
    getCurrentMonthBusinessSnapshot
  }
}

