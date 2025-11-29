<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Regional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Regional::class);
    }

    /**
     * Busca regional por nome
     */
    public function findByNome(string $nome): ?Regional
    {
        return $this->createQueryBuilder('r')
                    ->andWhere('r.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todas as regionais ordenadas por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('r')
                    ->orderBy('r.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

