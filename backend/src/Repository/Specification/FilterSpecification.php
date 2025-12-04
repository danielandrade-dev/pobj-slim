<?php

namespace App\Repository\Specification;

use App\Domain\DTO\FilterDTO;
use App\Domain\Enum\Cargo;
use App\Entity\Pobj\DEstrutura;
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
            // Converte ID para funcional se necessário
            $gerenteFuncional = $this->getFuncionalFromIdOrFuncional($qb, $gerente, Cargo::GERENTE);
            if ($gerenteFuncional) {
                $qb->andWhere("{$alias}.funcional = :gerenteFuncional")
                   ->setParameter('gerenteFuncional', $gerenteFuncional);
            }
        } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
            // Converte ID para funcional se necessário
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

    /**
     * Converte ID para funcional se necessário
     * Se o valor for numérico (ID), busca o funcional na tabela d_estrutura
     * Se o valor for string (funcional), retorna o próprio valor
     * 
     * @param QueryBuilder $qb QueryBuilder para obter EntityManager
     * @param mixed $idOrFuncional ID ou funcional
     * @param int $cargoId ID do cargo (GERENTE ou GERENTE_GESTAO)
     * @return string|null Funcional encontrado ou null se não encontrar
     */
    private function getFuncionalFromIdOrFuncional(QueryBuilder $qb, $idOrFuncional, int $cargoId): ?string
    {
        if ($idOrFuncional === null || $idOrFuncional === '') {
            return null;
        }

        // Se não for numérico, assume que já é um funcional
        if (!is_numeric($idOrFuncional)) {
            return (string)$idOrFuncional;
        }

        // Se for numérico, busca o funcional na tabela d_estrutura
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

