<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;

/**
 * Classe base abstrata para specifications compostas
 */
abstract class CompositeSpecification implements SpecificationInterface
{
    /**
     * Combina duas specifications com AND
     * 
     * @param SpecificationInterface $spec Specification a ser combinada
     * @return AndSpecification Nova specification combinada
     */
    public function and(SpecificationInterface $spec): AndSpecification
    {
        return new AndSpecification($this, $spec);
    }
    
    /**
     * Combina duas specifications com OR
     * 
     * @param SpecificationInterface $spec Specification a ser combinada
     * @return OrSpecification Nova specification combinada
     */
    public function or(SpecificationInterface $spec): OrSpecification
    {
        return new OrSpecification($this, $spec);
    }
    
    /**
     * Nega a specification
     * 
     * @return NotSpecification Nova specification negada
     */
    public function not(): NotSpecification
    {
        return new NotSpecification($this);
    }
}

