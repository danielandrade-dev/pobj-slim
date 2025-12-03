<?php

namespace App\Repository\Specification;

use App\Domain\DTO\FilterDTO;
use Doctrine\ORM\QueryBuilder;

/**
 * Specification baseada em FilterDTO
 * Aplica filtros de estrutura, produto e data
 */
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
        
        // Filtros de estrutura
        $segmento = $this->filters->getSegmento();
        $diretoria = $this->filters->getDiretoria();
        $regional = $this->filters->getRegional();
        $agencia = $this->filters->getAgencia();
        $gerente = $this->filters->getGerente();
        $gerenteGestao = $this->filters->getGerenteGestao();
        
        // Aplica apenas o filtro mais específico da hierarquia
        if ($gerente !== null && $gerente !== '') {
            $qb->andWhere("{$alias}.funcional = :gerenteFuncional")
               ->setParameter('gerenteFuncional', $gerente);
        } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
            $qb->andWhere("{$alias}.funcional = :gerenteGestaoFuncional")
               ->setParameter('gerenteGestaoFuncional', $gerenteGestao);
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
        
        // Filtros de produto
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
        
        // Filtros de data
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
}

