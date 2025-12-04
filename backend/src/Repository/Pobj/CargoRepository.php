<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\Cargo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CargoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cargo::class);
    }

    /**
     * Exemplo de método customizado
     */
    public function findByNome(string $nome): ?Cargo
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Exemplo de listar todos ordenados por nome
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('c')
                    ->orderBy('c.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

