<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaStatus::class);
    }

    /**
     * Busca status por label
     */
    public function findByLabel(string $label): ?OmegaStatus
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.label = :label')
                    ->setParameter('label', $label)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os status ordenados por ordem e label
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.ordem', 'ASC')
                    ->addOrderBy('s.label', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

