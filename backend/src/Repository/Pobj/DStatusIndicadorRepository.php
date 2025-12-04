<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DStatusIndicador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DStatusIndicadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DStatusIndicador::class);
    }

    
    public function findByStatus(string $status): ?DStatusIndicador
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.status = :status')
                    ->setParameter('status', $status)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedById(): array
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.id', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

