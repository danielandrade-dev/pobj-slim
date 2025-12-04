<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaCategoria;
use App\Entity\Omega\OmegaDepartamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaCategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaCategoria::class);
    }

    /**
     * Busca categorias por departamento
     */
    public function findByDepartamento(OmegaDepartamento $departamento): array
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.departamento = :departamento')
                    ->setParameter('departamento', $departamento)
                    ->orderBy('c.ordem', 'ASC')
                    ->addOrderBy('c.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Busca categorias por nome do departamento
     */
    public function findByDepartamentoNome(string $departamentoNome): array
    {
        return $this->createQueryBuilder('c')
                    ->innerJoin('c.departamento', 'd')
                    ->andWhere('d.nome = :departamentoNome')
                    ->setParameter('departamentoNome', $departamentoNome)
                    ->orderBy('c.ordem', 'ASC')
                    ->addOrderBy('c.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Lista todas as categorias ordenadas
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')
                    ->innerJoin('c.departamento', 'd')
                    ->orderBy('d.ordem', 'ASC')
                    ->addOrderBy('d.nome', 'ASC')
                    ->addOrderBy('c.ordem', 'ASC')
                    ->addOrderBy('c.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

