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

    /**
     * Busca segmento por nome
     */
    public function findByNome(string $nome): ?Segmento
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os segmentos ordenados por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

