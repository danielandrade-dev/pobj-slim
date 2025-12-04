<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Agencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AgenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agencia::class);
    }

    /**
     * Busca agência por nome
     */
    public function findByNome(string $nome): ?Agencia
    {
        return $this->createQueryBuilder('a')
                    ->andWhere('a.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todas as agências ordenadas por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('a')
                    ->orderBy('a.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

