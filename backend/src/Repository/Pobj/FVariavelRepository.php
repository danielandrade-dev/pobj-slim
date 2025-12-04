<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FVariavel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FVariavelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FVariavel::class);
    }

    
    public function findAllOrderedByDataAtualizacao(): array
    {
        return $this->createQueryBuilder('v')
                    ->orderBy('v.dtAtualizacao', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

