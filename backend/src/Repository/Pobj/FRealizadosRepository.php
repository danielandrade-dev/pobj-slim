<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FRealizados;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FRealizadosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FRealizados::class);
    }

    
    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('r')
                    ->orderBy('r.dataRealizado', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

