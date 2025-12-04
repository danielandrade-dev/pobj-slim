import { ref, computed } from 'vue'
import type { Period } from '../types'
import { getDefaultPeriod } from '../services/calendarioService'

export interface FilterState {
  segmento?: string
  diretoria?: string
  gerencia?: string
  agencia?: string
  ggestao?: string
  gerente?: string
  familia?: string
  indicador?: string
  subindicador?: string
  status?: string
}

const filterState = ref<FilterState>({})
const period = ref<Period>(getDefaultPeriod())
const filterTrigger = ref<number>(0)

const isEmptyValue = (value: string | undefined): boolean => {
  return !value || value === '' || value === 'Todos' || value === 'Todas'
}

export function useGlobalFilters() {
  function updateFilter(key: keyof FilterState, value: string | undefined): void {
    filterState.value[key] = isEmptyValue(value) ? undefined : value
  }

  function updatePeriod(newPeriod: Period): void {
    period.value = { ...newPeriod }
  }

  function clearFilters(): void {
    filterState.value = {}
  }

  function clearFilter(key: keyof FilterState): void {
    filterState.value[key] = undefined
  }

  function triggerFilter(): void {
    filterTrigger.value = Date.now()
  }

  return {
    filterState: computed(() => filterState.value),
    period: computed(() => period.value),
    filterTrigger: computed(() => filterTrigger.value),
    updateFilter,
    updatePeriod,
    clearFilters,
    clearFilter,
    triggerFilter
  }
}

