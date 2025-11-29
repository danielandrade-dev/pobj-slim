<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DCalendario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DCalendarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DCalendario::class);
    }

    /**
     * Busca calendário por data
     */
    public function findByData(\DateTimeInterface $data): ?DCalendario
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.data = :data')
                    ->setParameter('data', $data)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os calendários ordenados por data
     */
    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('c')
                    ->orderBy('c.data', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

