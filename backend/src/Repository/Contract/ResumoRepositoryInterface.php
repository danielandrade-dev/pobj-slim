<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;

/**
 * Interface para ResumoRepository
 */
interface ResumoRepositoryInterface
{
    /**
     * Retorna produtos com dados agregados (realizados, metas, pontos)
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return array Array de produtos com dados agregados
     */
    public function findProdutos(?FilterDTO $filters = null): array;
    
    /**
     * Retorna produtos com dados mensais para renderização do resumo-legacy
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return array Array de produtos com dados mensais
     */
    public function findProdutosMensais(?FilterDTO $filters = null): array;
    
    /**
     * Retorna variáveis ordenadas por data de atualização
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return array Array de variáveis
     */
    public function findVariavel(?FilterDTO $filters = null): array;
    
    /**
     * Retorna calendário disponível
     * 
     * @return array Array de datas do calendário
     */
    public function findCalendario(): array;
}

