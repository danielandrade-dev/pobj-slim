<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface para Specification Pattern
 * Permite construir queries de forma mais flexível e reutilizável
 */
interface SpecificationInterface
{
    /**
     * Aplica a especificação ao QueryBuilder
     * 
     * @param QueryBuilder $qb QueryBuilder a ser modificado
     * @param string $alias Alias da entidade principal
     * @return QueryBuilder QueryBuilder modificado
     */
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder;
    
    /**
     * Verifica se a especificação está satisfeita
     * 
     * @return bool True se a especificação está satisfeita
     */
    public function isSatisfiedBy(): bool;
}

