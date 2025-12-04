<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;

/**
 * Interface para FDetalhesRepository
 */
interface FDetalhesRepositoryInterface
{
    /**
     * Busca detalhes com filtros opcionais
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return array Array de DetalhesItemDTO
     */
    public function findDetalhes(?FilterDTO $filters = null): array;
    
    /**
     * Conta o total de registros que correspondem aos filtros
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return int Total de registros
     */
    public function countDetalhes(?FilterDTO $filters = null): int;
}

