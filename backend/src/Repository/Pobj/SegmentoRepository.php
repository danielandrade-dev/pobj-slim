<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Segmento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SegmentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Segmento::class);
    }

    
    public function findByNome(string $nome): ?Segmento
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

