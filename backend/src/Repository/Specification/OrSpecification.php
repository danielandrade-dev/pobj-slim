<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;


class OrSpecification extends CompositeSpecification
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
        $qb->orWhere($this->right->apply(clone $qb, $alias)->getDQLPart('where'));
        return $qb;
    }
    
    public function isSatisfiedBy(): bool
    {
        return $this->left->isSatisfiedBy() || $this->right->isSatisfiedBy();
    }
}

