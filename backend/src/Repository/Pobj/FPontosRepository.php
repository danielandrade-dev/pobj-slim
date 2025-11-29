<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FPontos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FPontosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FPontos::class);
    }

    /**
     * Lista todos os pontos ordenados por data
     */
    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('p')
                    ->orderBy('p.dataRealizado', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

