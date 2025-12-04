<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaDepartamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaDepartamentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaDepartamento::class);
    }

    /**
     * Busca departamento por nome
     */
    public function findByNome(string $nome): ?OmegaDepartamento
    {
        return $this->createQueryBuilder('d')
                    ->andWhere('d.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Busca departamento por nome_id
     */
    public function findByNomeId(string $nomeId): ?OmegaDepartamento
    {
        return $this->createQueryBuilder('d')
                    ->andWhere('d.nomeId = :nomeId')
                    ->setParameter('nomeId', $nomeId)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os departamentos ordenados
     */
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('d')
                    ->orderBy('d.ordem', 'ASC')
                    ->addOrderBy('d.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
