<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FMeta::class);
    }

    /**
     * Lista todas as metas ordenadas por data
     */
    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('m')
                    ->orderBy('m.dataMeta', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

