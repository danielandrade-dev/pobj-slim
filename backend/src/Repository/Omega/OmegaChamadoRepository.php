<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaChamado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaChamadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaChamado::class);
    }

    /**
     * Lista todos os chamados ordenados por data de atualização
     */
    public function findAllOrderedByUpdated(): array
    {
        return $this->createQueryBuilder('c')
                    ->orderBy('c.updated', 'DESC')
                    ->addOrderBy('c.opened', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}

