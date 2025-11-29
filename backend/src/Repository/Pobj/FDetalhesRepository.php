<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FDetalhes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FDetalhesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FDetalhes::class);
    }

    /**
     * Lista todos os detalhes ordenados por data
     */
    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('d')
                    ->orderBy('d.data', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

