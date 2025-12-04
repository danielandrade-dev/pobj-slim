<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;

/**
 * Interface base para todos os repositories
 */
interface RepositoryInterface
{
    /**
     * Busca entidades com filtros opcionais
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return array Resultado da busca
     */
    public function findByFilters(?FilterDTO $filters = null): array;
}

