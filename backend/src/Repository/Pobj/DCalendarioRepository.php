<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DCalendario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DCalendarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DCalendario::class);
    }

    public function findAllOrderedByData(): array
    {
        return $this->createQueryBuilder('c')
                    ->select('c.data, c.ano, c.mes, c.mesNome, c.dia, c.diaDaSemana, c.semana, c.trimestre, c.semestre, c.ehDiaUtil')
                    ->orderBy('c.data', 'ASC')
                    ->getQuery()
                    ->getArrayResult();
    }
}

