<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\FVariavel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FVariavelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FVariavel::class);
    }

    /**
     * Lista todas as variáveis ordenadas por data de atualização
     */
    public function findAllOrderedByDataAtualizacao(): array
    {
        return $this->createQueryBuilder('v')
                    ->orderBy('v.dtAtualizacao', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

