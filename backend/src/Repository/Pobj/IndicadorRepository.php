<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Indicador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IndicadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indicador::class);
    }

    /**
     * Busca indicador por nome
     */
    public function findByNome(string $nome): ?Indicador
    {
        return $this->createQueryBuilder('i')
                    ->andWhere('i.nmIndicador = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os indicadores ordenados por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('i')
                    ->orderBy('i.nmIndicador', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

