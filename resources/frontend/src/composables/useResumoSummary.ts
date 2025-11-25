import { computed, type Ref, type ComputedRef } from 'vue'
import { useFilteredProdutos } from './useFilteredProdutos'
import { useVariavel, type VariavelFilters } from './useVariavel'
import type { Period } from '../types'
import type { FilterState } from './useFilteredProdutos'

/**
 * Composable para calcular o resumo geral (indicadores, pontos, variável)
 */
export function useResumoSummary(
  filterState: Ref<FilterState> | ComputedRef<FilterState>,
  period: Ref<Period> | ComputedRef<Period>
) {
  const { produtosPorFamilia } = useFilteredProdutos(filterState, period)

  // Converte filtros para formato de variável
  const variavelFilters = computed<VariavelFilters | null>(() => {
    const filters: VariavelFilters = {}
    
    if (filterState.value.segmento && 
        filterState.value.segmento !== '' && 
        filterState.value.segmento !== 'Todos' && 
        filterState.value.segmento !== 'Todas') {
      filters.segmento = filterState.value.segmento
    }
    
    if (filterState.value.diretoria && 
        filterState.value.diretoria !== '' && 
        filterState.value.diretoria !== 'Todos' && 
        filterState.value.diretoria !== 'Todas') {
      filters.diretoria = filterState.value.diretoria
    }
    
    if (filterState.value.gerencia && 
        filterState.value.gerencia !== '' && 
        filterState.value.gerencia !== 'Todos' && 
        filterState.value.gerencia !== 'Todas') {
      filters.regional = filterState.value.gerencia
    }
    
    if (filterState.value.agencia && 
        filterState.value.agencia !== '' && 
        filterState.value.agencia !== 'Todos' && 
        filterState.value.agencia !== 'Todas') {
      filters.agencia = filterState.value.agencia
    }
    
    if (filterState.value.ggestao && 
        filterState.value.ggestao !== '' && 
        filterState.value.ggestao !== 'Todos' && 
        filterState.value.ggestao !== 'Todas') {
      filters.gerenteGestao = filterState.value.ggestao
    }
    
    if (filterState.value.gerente && 
        filterState.value.gerente !== '' && 
        filterState.value.gerente !== 'Todos' && 
        filterState.value.gerente !== 'Todas') {
      filters.gerente = filterState.value.gerente
    }
    
    if (period.value.start) {
      filters.dataInicio = period.value.start
    }
    
    if (period.value.end) {
      filters.dataFim = period.value.end
    }
    
    return filters
  })

  const { summary: variavelSummary } = useVariavel(variavelFilters)

  // Calcula summary dos produtos
  const summary = computed(() => {
    const familias = produtosPorFamilia.value || []
    
    let indicadoresAtingidos = 0
    let indicadoresTotal = 0
    let pontosAtingidos = 0
    let pontosTotal = 0

    familias.forEach(familia => {
      familia.items.forEach(item => {
        indicadoresTotal++
        if (item.atingido) {
          indicadoresAtingidos++
        }
        
        const pontosMeta = item.pontosMeta || 0
        const pontosReal = Math.max(0, item.pontos || 0)
        
        pontosTotal += pontosMeta
        pontosAtingidos += Math.min(pontosReal, pontosMeta)
      })
    })

    return {
      indicadoresAtingidos,
      indicadoresTotal,
      pontosAtingidos,
      pontosPossiveis: pontosTotal,
      varPossivel: variavelSummary.value.varPossivel,
      varAtingido: variavelSummary.value.varAtingido
    }
  })

  return {
    summary
  }
}

