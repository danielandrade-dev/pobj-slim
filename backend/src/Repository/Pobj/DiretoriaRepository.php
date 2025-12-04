<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Diretoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DiretoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diretoria::class);
    }

    
    public function findByNome(string $nome): ?Diretoria
    {
        return $this->createQueryBuilder('d')
                    ->andWhere('d.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('d')
                    ->orderBy('d.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

