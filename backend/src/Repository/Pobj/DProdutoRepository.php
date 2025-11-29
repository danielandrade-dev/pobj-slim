<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DProduto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DProdutoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DProduto::class);
    }

    /**
     * Lista todos os produtos ordenados por família, indicador e subindicador
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
                    ->orderBy('p.familia', 'ASC')
                    ->addOrderBy('p.indicador', 'ASC')
                    ->addOrderBy('p.subindicador', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

