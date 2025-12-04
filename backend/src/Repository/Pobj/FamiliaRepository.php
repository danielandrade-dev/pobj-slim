<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Familia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FamiliaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Familia::class);
    }

    
    public function findByNome(string $nome): ?Familia
    {
        return $this->createQueryBuilder('f')
                    ->andWhere('f.nmFamilia = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('f')
                    ->orderBy('f.nmFamilia', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

