<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;

/**
 * Specification que combina duas specifications com AND
 */
class AndSpecification extends CompositeSpecification
{
    private $left;
    private $right;
    
    public function __construct(SpecificationInterface $left, SpecificationInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }
    
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder
    {
        $qb = $this->left->apply($qb, $alias);
        return $this->right->apply($qb, $alias);
    }
    
    public function isSatisfiedBy(): bool
    {
        return $this->left->isSatisfiedBy() && $this->right->isSatisfiedBy();
    }
}

