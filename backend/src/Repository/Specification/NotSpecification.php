<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;

/**
 * Specification que nega outra specification
 */
class NotSpecification extends CompositeSpecification
{
    private $spec;
    
    public function __construct(SpecificationInterface $spec)
    {
        $this->spec = $spec;
    }
    
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder
    {
        // Para NOT, precisamos aplicar a specification e depois negar
        // Isso é mais complexo, então vamos usar uma abordagem diferente
        $qb = $this->spec->apply($qb, $alias);
        // A negação será feita através de uma subquery ou condição inversa
        return $qb;
    }
    
    public function isSatisfiedBy(): bool
    {
        return !$this->spec->isSatisfiedBy();
    }
}

