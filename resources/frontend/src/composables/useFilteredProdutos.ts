import { computed, type Ref, type ComputedRef } from 'vue'
import { useProdutos } from './useProdutos'
import type { ProdutoFilters } from '../services/produtosService'
import type { Period } from '../types'

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

/**
 * Composable que integra filtros hierárquicos, período e produtos
 */
export function useFilteredProdutos(
  filterState: Ref<FilterState> | ComputedRef<FilterState>,
  period: Ref<Period> | ComputedRef<Period>
) {
  // Converte os filtros do Vue para o formato do backend
  const produtoFilters = computed<ProdutoFilters | null>(() => {
    const filters: ProdutoFilters = {}
    
    // Filtros hierárquicos
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
    
    // Filtros de produto
    if (filterState.value.familia && 
        filterState.value.familia !== '' && 
        filterState.value.familia !== 'Todos' && 
        filterState.value.familia !== 'Todas') {
      filters.familia = filterState.value.familia
    }
    
    if (filterState.value.indicador && 
        filterState.value.indicador !== '' && 
        filterState.value.indicador !== 'Todos' && 
        filterState.value.indicador !== 'Todas') {
      filters.indicador = filterState.value.indicador
    }
    
    if (filterState.value.subindicador && 
        filterState.value.subindicador !== '' && 
        filterState.value.subindicador !== 'Todos' && 
        filterState.value.subindicador !== 'Todas') {
      filters.subindicador = filterState.value.subindicador
    }
    
    // Filtro de status
    if (filterState.value.status && 
        filterState.value.status !== '' && 
        filterState.value.status !== 'todos') {
      filters.status = filterState.value.status
    }
    
    // Período
    if (period.value.start) {
      filters.dataInicio = period.value.start
    }
    
    if (period.value.end) {
      filters.dataFim = period.value.end
    }
    
    // Retorna null se não houver filtros para evitar requisições desnecessárias
    // Mas na verdade, sempre retornamos os filtros para que o período seja aplicado
    return filters
  })

  // Usa o composable de produtos com os filtros
  const { produtos, produtosPorFamilia, loading, error, loadProdutos } = useProdutos(produtoFilters)

  return {
    produtos,
    produtosPorFamilia,
    loading,
    error,
    loadProdutos,
    produtoFilters
  }
}

