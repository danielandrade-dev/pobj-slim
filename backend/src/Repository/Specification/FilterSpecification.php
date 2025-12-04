<?php

namespace App\Repository\Specification;

use App\Domain\DTO\FilterDTO;
use App\Domain\Enum\Cargo;
use App\Entity\Pobj\DEstrutura;
use Doctrine\ORM\QueryBuilder;


class FilterSpecification extends CompositeSpecification
{
    private $filters;
    
    public function __construct(?FilterDTO $filters = null)
    {
        $this->filters = $filters;
    }
    
    public function apply(QueryBuilder $qb, string $alias = 'e'): QueryBuilder
    {
        if (!$this->filters) {
            return $qb;
        }
        
                $segmento = $this->filters->getSegmento();
        $diretoria = $this->filters->getDiretoria();
        $regional = $this->filters->getRegional();
        $agencia = $this->filters->getAgencia();
        $gerente = $this->filters->getGerente();
        $gerenteGestao = $this->filters->getGerenteGestao();
        
                if ($gerente !== null && $gerente !== '') {
                        $gerenteFuncional = $this->getFuncionalFromIdOrFuncional($qb, $gerente, Cargo::GERENTE);
            if ($gerenteFuncional) {
                $qb->andWhere("{$alias}.funcional = :gerenteFuncional")
                   ->setParameter('gerenteFuncional', $gerenteFuncional);
            }
        } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
                        $gerenteGestaoFuncional = $this->getFuncionalFromIdOrFuncional($qb, $gerenteGestao, Cargo::GERENTE_GESTAO);
            if ($gerenteGestaoFuncional) {
                $qb->andWhere("{$alias}.funcional = :gerenteGestaoFuncional")
                   ->setParameter('gerenteGestaoFuncional', $gerenteGestaoFuncional);
            }
        } elseif ($agencia !== null && $agencia !== '') {
            $qb->andWhere("{$alias}.agencia = :agenciaId")
               ->setParameter('agenciaId', $agencia);
        } elseif ($regional !== null && $regional !== '') {
            $qb->andWhere("{$alias}.regional = :regionalId")
               ->setParameter('regionalId', $regional);
        } elseif ($diretoria !== null && $diretoria !== '') {
            $qb->andWhere("{$alias}.diretoria = :diretoriaId")
               ->setParameter('diretoriaId', $diretoria);
        } elseif ($segmento !== null && $segmento !== '') {
            $qb->andWhere("{$alias}.segmento = :segmentoId")
               ->setParameter('segmentoId', $segmento);
        }
        
                $familia = $this->filters->getFamilia();
        $indicador = $this->filters->getIndicador();
        $subindicador = $this->filters->getSubindicador();
        
        if ($subindicador !== null && $subindicador !== '') {
            $qb->andWhere("{$alias}.subindicador = :subindicadorId")
               ->setParameter('subindicadorId', $subindicador);
        } elseif ($indicador !== null && $indicador !== '') {
            $qb->andWhere("{$alias}.indicador = :indicadorId")
               ->setParameter('indicadorId', $indicador);
        } elseif ($familia !== null && $familia !== '') {
            $qb->andWhere("{$alias}.familia = :familiaId")
               ->setParameter('familiaId', $familia);
        }
        
                $dataInicio = $this->filters->getDataInicio();
        $dataFim = $this->filters->getDataFim();
        
        if ($dataInicio !== null && $dataInicio !== '') {
            $qb->andWhere("{$alias}.dataRealizado >= :dataInicio")
               ->setParameter('dataInicio', $dataInicio);
        }
        
        if ($dataFim !== null && $dataFim !== '') {
            $qb->andWhere("{$alias}.dataRealizado <= :dataFim")
               ->setParameter('dataFim', $dataFim);
        }
        
        return $qb;
    }
    
    public function isSatisfiedBy(): bool
    {
        return $this->filters !== null && $this->filters->hasAnyFilter();
    }

    
    private function getFuncionalFromIdOrFuncional(QueryBuilder $qb, $idOrFuncional, int $cargoId): ?string
    {
        if ($idOrFuncional === null || $idOrFuncional === '') {
            return null;
        }

                if (!is_numeric($idOrFuncional)) {
            return (string)$idOrFuncional;
        }

                $em = $qb->getEntityManager();
        $metadata = $em->getClassMetadata(DEstrutura::class);
        $dEstruturaTable = $metadata->getTableName();
        $conn = $em->getConnection();
        
        $sql = "SELECT funcional FROM {$dEstruturaTable} 
                WHERE id = :id AND cargo_id = :cargoId 
                LIMIT 1";
        
        $result = $conn->executeQuery($sql, [
            'id' => (int)$idOrFuncional,
            'cargoId' => $cargoId
        ]);
        
        $row = $result->fetchAssociative();
        $result->free();
        
        return $row ? ($row['funcional'] ?? null) : null;
    }
}

