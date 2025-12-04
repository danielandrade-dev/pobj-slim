<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;


abstract class CompositeSpecification implements SpecificationInterface
{
    
    public function and(SpecificationInterface $spec): AndSpecification
    {
        return new AndSpecification($this, $spec);
    }
    
    
    public function or(SpecificationInterface $spec): OrSpecification
    {
        return new OrSpecification($this, $spec);
    }
    
    
    public function not(): NotSpecification
    {
        return new NotSpecification($this);
    }
}

