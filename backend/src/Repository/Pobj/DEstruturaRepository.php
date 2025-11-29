<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DEstrutura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DEstruturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DEstrutura::class);
    }

    /**
     * Busca estrutura por funcional
     */
    public function findByFuncional(string $funcional): ?DEstrutura
    {
        return $this->createQueryBuilder('e')
                    ->andWhere('e.funcional = :funcional')
                    ->setParameter('funcional', $funcional)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todas as estruturas ordenadas por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('e')
                    ->orderBy('e.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

