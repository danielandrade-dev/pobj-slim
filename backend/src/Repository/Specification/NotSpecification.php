<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;


class NotSpecification extends CompositeSpecification
{
    private $spec;
    
    public function __construct(SpecificationInterface $spec)
    {
        $this->spec = $spec;
    }
    
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder
    {
                        $qb = $this->spec->apply($qb, $alias);
                return $qb;
    }
    
    public function isSatisfiedBy(): bool
    {
        return !$this->spec->isSatisfiedBy();
    }
}

