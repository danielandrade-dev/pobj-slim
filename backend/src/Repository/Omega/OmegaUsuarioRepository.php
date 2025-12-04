<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaUsuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaUsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaUsuario::class);
    }

    
    public function findByNome(string $nome): ?OmegaUsuario
    {
        return $this->createQueryBuilder('u')
                    ->andWhere('u.nome = :nome')
                    ->setParameter('nome', $nome)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findByFuncional(string $funcional): ?OmegaUsuario
    {
        return $this->createQueryBuilder('u')
                    ->andWhere('u.funcional = :funcional')
                    ->setParameter('funcional', $funcional)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    
    public function findAllOrderedByNome(): array
    {
        return $this->createQueryBuilder('u')
                    ->orderBy('u.nome', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}

