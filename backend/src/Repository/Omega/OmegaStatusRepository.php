<?php

namespace App\Repository\Omega;

use App\Entity\Omega\OmegaStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OmegaStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OmegaStatus::class);
    }

    /**
     * Busca status por label
     */
    public function findByLabel(string $label): ?OmegaStatus
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.label = :label')
                    ->setParameter('label', $label)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Lista todos os status ordenados por ordem e label
     */
    public function findAllOrdered(): array
    {
        $statuses = $this->createQueryBuilder('s')
                    ->orderBy('s.ordem', 'ASC')
                    ->addOrderBy('s.label', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        // Carrega departamentos manualmente se necessário
        $departamentoIds = array_filter(array_map(function($status) {
            return $status->getDepartamentoId();
        }, $statuses));
        
        if (!empty($departamentoIds)) {
            $departamentoRepository = $this->getEntityManager()->getRepository(\App\Entity\Omega\OmegaDepartamento::class);
            $departamentos = [];
            foreach ($departamentoIds as $nomeId) {
                $dept = $departamentoRepository->findByNomeId($nomeId);
                if ($dept) {
                    $departamentos[$nomeId] = $dept;
                }
            }
            
            // Associa departamentos aos status
            foreach ($statuses as $status) {
                $nomeId = $status->getDepartamentoId();
                if ($nomeId && isset($departamentos[$nomeId])) {
                    $status->setDepartamento($departamentos[$nomeId]);
                }
            }
        }
        
        return $statuses;
    }
}

