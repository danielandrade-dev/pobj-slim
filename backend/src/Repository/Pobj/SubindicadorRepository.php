<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Subindicador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubindicadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subindicador::class);
    }

    
    public function findByNome(string $nome): ?Subindicador
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.nmSubindicador = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.nmSubindicador', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

