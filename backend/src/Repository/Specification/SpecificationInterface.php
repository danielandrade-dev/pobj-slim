<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;


interface SpecificationInterface
{
    
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder;
    
    
    public function isSatisfiedBy(): bool;
}

