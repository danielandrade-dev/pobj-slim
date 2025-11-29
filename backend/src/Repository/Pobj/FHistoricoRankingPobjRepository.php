<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FHistoricoRankingPobj;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FHistoricoRankingPobjRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FHistoricoRankingPobj::class);
    }

    /**
     * Lista todo o histórico ordenado por ranking
     */
    public function findAllOrderedByRanking(): array
    {
        return $this->createQueryBuilder('h')
                    ->orderBy('h.ranking', 'ASC')
                    ->addOrderBy('h.realizado', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

