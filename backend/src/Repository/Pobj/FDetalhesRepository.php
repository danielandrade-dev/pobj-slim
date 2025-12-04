<?php

namespace App\Repository\Pobj;

use App\Domain\DTO\FilterDTO;
use App\Entity\Pobj\FDetalhes;
use App\Entity\Pobj\FRealizados;
use App\Entity\Pobj\DCalendario;
use App\Entity\Pobj\DEstrutura;
use App\Entity\Pobj\DProduto;
use App\Entity\Pobj\FMeta;
use App\Entity\Pobj\Segmento;
use App\Entity\Pobj\Diretoria;
use App\Entity\Pobj\Regional;
use App\Entity\Pobj\Agencia;
use App\Entity\Pobj\Familia;
use App\Entity\Pobj\Indicador;
use App\Entity\Pobj\Subindicador;
use App\Domain\Enum\Cargo;
use App\Domain\DTO\Detalhes\DetalhesItemDTO;
use App\Repository\Contract\FDetalhesRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FDetalhesRepository extends ServiceEntityRepository implements FDetalhesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FDetalhes::class);
    }

    /**
     * Retorna o nome da tabela de uma entidade
     */
    private function getTableName(string $entityClass): string
    {
        return $this->getEntityManager()
            ->getClassMetadata($entityClass)
            ->getTableName();
    }

    /**
     * Constrói a cláusula WHERE baseada nos filtros de estrutura
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @param array &$params Parâmetros da query (passado por referência)
     * @return string Cláusula WHERE construída
     */
    private function buildEstruturaWhereClause(?FilterDTO $filters, array &$params): string
    {
        if (!$filters) {
            return '';
        }

        $whereClause = '';
        $gerente = $filters->getGerente();
        $gerenteGestao = $filters->getGerenteGestao();
        $agencia = $filters->getAgencia();
        $regional = $filters->getRegional();
        $diretoria = $filters->getDiretoria();
        $segmento = $filters->getSegmento();

        if ($gerente !== null && $gerente !== '') {
            // Converte ID para funcional se necessário
            $gerenteFuncional = $this->getFuncionalFromIdOrFuncional($gerente, Cargo::GERENTE);
            if ($gerenteFuncional) {
                $whereClause .= " AND fr.funcional = :gerenteFuncional";
                $params['gerenteFuncional'] = $gerenteFuncional;
            }
        } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
            // Converte ID para funcional se necessário
            $gerenteGestaoFuncional = $this->getFuncionalFromIdOrFuncional($gerenteGestao, Cargo::GERENTE_GESTAO);
            if ($gerenteGestaoFuncional) {
                $dEstruturaTable = $this->getTableName(DEstrutura::class);
                $whereClause .= " AND EXISTS (
                    SELECT 1 FROM {$dEstruturaTable} AS ggestao 
                    WHERE ggestao.funcional = :gerenteGestaoFuncional
                    AND ggestao.cargo_id = :cargoGerenteGestao
                    AND ggestao.segmento_id = est.segmento_id
                    AND ggestao.diretoria_id = est.diretoria_id
                    AND ggestao.regional_id = est.regional_id
                    AND ggestao.agencia_id = est.agencia_id
                )";
                $params['gerenteGestaoFuncional'] = $gerenteGestaoFuncional;
                $params['cargoGerenteGestao'] = Cargo::GERENTE_GESTAO;
            }
        } else {
            if ($agencia !== null && $agencia !== '') {
                $whereClause .= " AND est.agencia_id = :agencia";
                $params['agencia'] = $agencia;
            } elseif ($regional !== null && $regional !== '') {
                $whereClause .= " AND est.regional_id = :regional";
                $params['regional'] = $regional;
            } elseif ($diretoria !== null && $diretoria !== '') {
                $whereClause .= " AND est.diretoria_id = :diretoria";
                $params['diretoria'] = $diretoria;
            } elseif ($segmento !== null && $segmento !== '') {
                $whereClause .= " AND est.segmento_id = :segmento";
                $params['segmento'] = $segmento;
            }
        }

        return $whereClause;
    }

    /**
     * Constrói a cláusula WHERE baseada nos filtros de produto
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @param array &$params Parâmetros da query (passado por referência)
     * @return string Cláusula WHERE construída
     */
    private function buildProdutoWhereClause(?FilterDTO $filters, array &$params): string
    {
        if (!$filters) {
            return '';
        }

        $whereClause = '';
        $familia = $filters->getFamilia();
        $indicador = $filters->getIndicador();
        $subindicador = $filters->getSubindicador();

        if ($subindicador !== null && $subindicador !== '') {
            $whereClause .= " AND prod.subindicador_id = :subindicador";
            $params['subindicador'] = $subindicador;
        } elseif ($indicador !== null && $indicador !== '') {
            $whereClause .= " AND prod.indicador_id = :indicador";
            $params['indicador'] = $indicador;
        } elseif ($familia !== null && $familia !== '') {
            $whereClause .= " AND prod.familia_id = :familia";
            $params['familia'] = $familia;
        }

        return $whereClause;
    }

    /**
     * Constrói a cláusula WHERE baseada nos filtros de data
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @param array &$params Parâmetros da query (passado por referência)
     * @return string Cláusula WHERE construída
     */
    private function buildDataWhereClause(?FilterDTO $filters, array &$params): string
    {
        if (!$filters) {
            return '';
        }

        $whereClause = '';
        $dataInicio = $filters->getDataInicio();
        $dataFim = $filters->getDataFim();

        if ($dataInicio !== null && $dataInicio !== '') {
            $whereClause .= " AND fr.data_realizado >= :dataInicio";
            $params['dataInicio'] = $dataInicio;
        }

        if ($dataFim !== null && $dataFim !== '') {
            $whereClause .= " AND fr.data_realizado <= :dataFim";
            $params['dataFim'] = $dataFim;
        }

        return $whereClause;
    }

    /**
     * Constrói a query SQL completa para buscar detalhes
     * 
     * @param array $tableNames Nomes das tabelas
     * @param string $whereClause Cláusula WHERE completa
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return string Query SQL construída
     */
    private function buildDetalhesQuery(array $tableNames, string $whereClause, int $limit, int $offset): string
    {
        $sql = "SELECT
                    COALESCE(det.registro_id, fr.id_contrato) AS registro_id,
                    COALESCE(det.competencia, fr.data_realizado) AS data,
                    COALESCE(det.competencia, fr.data_realizado) AS competencia,
                    YEAR(COALESCE(det.competencia, fr.data_realizado)) AS ano,
                    MONTH(COALESCE(det.competencia, fr.data_realizado)) AS mes,
                    CASE MONTH(COALESCE(det.competencia, fr.data_realizado))
                        WHEN 1 THEN 'Janeiro'
                        WHEN 2 THEN 'Fevereiro'
                        WHEN 3 THEN 'Março'
                        WHEN 4 THEN 'Abril'
                        WHEN 5 THEN 'Maio'
                        WHEN 6 THEN 'Junho'
                        WHEN 7 THEN 'Julho'
                        WHEN 8 THEN 'Agosto'
                        WHEN 9 THEN 'Setembro'
                        WHEN 10 THEN 'Outubro'
                        WHEN 11 THEN 'Novembro'
                        WHEN 12 THEN 'Dezembro'
                    END AS mes_nome,
                    est.segmento_id,
                    seg.nome AS segmento,
                    est.diretoria_id,
                    dir.nome AS diretoria_nome,
                    est.regional_id AS gerencia_id,
                    reg.nome AS gerencia_nome,
                    est.agencia_id,
                    ag.nome AS agencia_nome,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN fr.funcional
                        ELSE NULL
                    END AS gerente_id,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN est.nome
                        ELSE NULL
                    END AS gerente_nome,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente AND est.agencia_id IS NOT NULL THEN (
                            SELECT ggestao.funcional 
                            FROM {$tableNames['dEstrutura']} AS ggestao
                            WHERE ggestao.agencia_id = est.agencia_id
                            AND ggestao.cargo_id = :cargoGerenteGestao
                            LIMIT 1
                        )
                        WHEN est.cargo_id = :cargoGerenteGestao THEN fr.funcional
                        ELSE NULL
                    END AS gerente_gestao_id,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente AND est.agencia_id IS NOT NULL THEN (
                            SELECT ggestao.nome 
                            FROM {$tableNames['dEstrutura']} AS ggestao
                            WHERE ggestao.agencia_id = est.agencia_id
                            AND ggestao.cargo_id = :cargoGerenteGestao
                            LIMIT 1
                        )
                        WHEN est.cargo_id = :cargoGerenteGestao THEN est.nome
                        ELSE NULL
                    END AS gerente_gestao_nome,
                    prod.familia_id,
                    fam.nm_familia AS familia_nome,
                    prod.indicador_id AS id_indicador,
                    ind.nm_indicador AS ds_indicador,
                    prod.subindicador_id AS id_subindicador,
                    sub.nm_subindicador AS subindicador,
                    prod.peso,
                    fr.realizado AS valor_realizado,
                    meta.meta_mensal AS valor_meta,
                    meta.meta_mensal AS meta_mensal,
                    det.canal_venda,
                    det.tipo_venda,
                    det.condicao_pagamento AS modalidade_pagamento,
                    det.dt_vencimento,
                    det.dt_cancelamento,
                    det.motivo_cancelamento,
                    det.status_id
                FROM {$tableNames['fRealizados']} AS fr
                INNER JOIN {$tableNames['dEstrutura']} AS est ON est.funcional = fr.funcional
                INNER JOIN {$tableNames['dProdutos']} AS prod ON prod.id = fr.produto_id
                LEFT JOIN {$tableNames['segmento']} AS seg ON seg.id = est.segmento_id
                LEFT JOIN {$tableNames['diretoria']} AS dir ON dir.id = est.diretoria_id
                LEFT JOIN {$tableNames['regional']} AS reg ON reg.id = est.regional_id
                LEFT JOIN {$tableNames['agencia']} AS ag ON ag.id = est.agencia_id
                LEFT JOIN {$tableNames['familia']} AS fam ON fam.id = prod.familia_id
                LEFT JOIN {$tableNames['indicador']} AS ind ON ind.id = prod.indicador_id
                LEFT JOIN {$tableNames['subindicador']} AS sub ON sub.id = prod.subindicador_id
                LEFT JOIN {$tableNames['fMeta']} AS meta
                    ON meta.funcional = fr.funcional
                    AND meta.produto_id = fr.produto_id
                    AND DATE_FORMAT(meta.data_meta, '%Y-%m') = DATE_FORMAT(fr.data_realizado, '%Y-%m')
                LEFT JOIN {$tableNames['fDetalhes']} AS det ON det.contrato_id = fr.id_contrato
                WHERE 1=1 {$whereClause}
                ORDER BY est.diretoria_id, est.regional_id, est.agencia_id, est.nome, prod.familia_id, prod.indicador_id, prod.subindicador_id, fr.id_contrato
                LIMIT {$limit} OFFSET {$offset}";

        return $sql;
    }

    /**
     * Busca detalhes com filtros opcionais, baseado no backend antigo
     */
    public function findDetalhes(?FilterDTO $filters = null): array
    {
        $fRealizadosTable = $this->getTableName(FRealizados::class);
        $dCalendarioTable = $this->getTableName(DCalendario::class);
        $dEstruturaTable = $this->getTableName(DEstrutura::class);
        $dProdutosTable = $this->getTableName(DProduto::class);
        $fMetaTable = $this->getTableName(FMeta::class);
        $fDetalhesTable = $this->getTableName(FDetalhes::class);
        $segmentoTable = $this->getTableName(Segmento::class);
        $diretoriaTable = $this->getTableName(Diretoria::class);
        $regionalTable = $this->getTableName(Regional::class);
        $agenciaTable = $this->getTableName(Agencia::class);
        $familiaTable = $this->getTableName(Familia::class);
        $indicadorTable = $this->getTableName(Indicador::class);
        $subindicadorTable = $this->getTableName(Subindicador::class);

        $params = [];
        
        // Adiciona os parâmetros do cargo para o CASE e subqueries
        $params['cargoGerente'] = Cargo::GERENTE;
        $params['cargoGerenteGestao'] = Cargo::GERENTE_GESTAO;

        // Constrói cláusulas WHERE usando métodos específicos
        $whereClause = $this->buildEstruturaWhereClause($filters, $params);
        $whereClause .= $this->buildProdutoWhereClause($filters, $params);
        $whereClause .= $this->buildDataWhereClause($filters, $params);

        // Prepara nomes das tabelas
        $tableNames = [
            'fRealizados' => $fRealizadosTable,
            'dEstrutura' => $dEstruturaTable,
            'dProdutos' => $dProdutosTable,
            'fMeta' => $fMetaTable,
            'fDetalhes' => $fDetalhesTable,
            'segmento' => $segmentoTable,
            'diretoria' => $diretoriaTable,
            'regional' => $regionalTable,
            'agencia' => $agenciaTable,
            'familia' => $familiaTable,
            'indicador' => $indicadorTable,
            'subindicador' => $subindicadorTable,
        ];

        // Aplica paginação se solicitada, caso contrário usa limite padrão
        $limit = 1000;
        $offset = 0;
        
        if ($filters && $filters->hasPagination()) {
            $limit = $filters->limit;
            $offset = $filters->getOffset();
        }
        
        $limit = max(1, min(5000, (int)$limit));
        $offset = max(0, (int)$offset);
        
        // Constrói a query SQL completa usando método específico
        $sql = $this->buildDetalhesQuery($tableNames, $whereClause, $limit, $offset);

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeQuery($sql, $params);
        
        // Processa resultados de forma mais eficiente em memória
        // Usa fetchAssociative() em loop ao invés de fetchAllAssociative() para reduzir uso de memória
        $detalhes = [];
        while ($row = $result->fetchAssociative()) {
            // Formata datas
            $data = $row['data'] ?? null;
            if ($data instanceof \DateTimeInterface) {
                $data = $data->format('Y-m-d');
            } elseif (!is_string($data) || $data === '') {
                $data = null;
            }

            $competencia = $row['competencia'] ?? null;
            if ($competencia instanceof \DateTimeInterface) {
                $competencia = $competencia->format('Y-m-d');
            } elseif (!is_string($competencia) || $competencia === '') {
                $competencia = null;
            }

            $dtVencimento = $row['dt_vencimento'] ?? null;
            if ($dtVencimento instanceof \DateTimeInterface) {
                $dtVencimento = $dtVencimento->format('Y-m-d');
            } elseif (!is_string($dtVencimento) || $dtVencimento === '') {
                $dtVencimento = null;
            }

            $dtCancelamento = $row['dt_cancelamento'] ?? null;
            if ($dtCancelamento instanceof \DateTimeInterface) {
                $dtCancelamento = $dtCancelamento->format('Y-m-d');
            } elseif (!is_string($dtCancelamento) || $dtCancelamento === '') {
                $dtCancelamento = null;
            }

            $dto = new DetalhesItemDTO(
                $row['registro_id'] ?? null,
                $row['registro_id'] ?? null,
                $data,
                $competencia,
                $row['ano'] ?? null,
                $row['mes'] ?? null,
                $row['mes_nome'] ?? null,
                $row['segmento_id'] ? (string)$row['segmento_id'] : null,
                $row['segmento'] ?? null,
                $row['diretoria_id'] ? (string)$row['diretoria_id'] : null,
                $row['diretoria_nome'] ?? null,
                $row['gerencia_id'] ? (string)$row['gerencia_id'] : null,
                $row['gerencia_nome'] ?? null,
                $row['agencia_id'] ? (string)$row['agencia_id'] : null,
                $row['agencia_nome'] ?? null,
                $row['gerente_id'] ?? null,
                $row['gerente_nome'] ?? null,
                $row['gerente_gestao_id'] ?? null,
                $row['gerente_gestao_nome'] ?? null,
                $row['familia_id'] ? (string)$row['familia_id'] : null,
                $row['familia_nome'] ?? null,
                $row['id_indicador'] ? (string)$row['id_indicador'] : null,
                $row['ds_indicador'] ?? null,
                $row['id_subindicador'] ? (string)$row['id_subindicador'] : null,
                $row['subindicador'] ?? null,
                $row['peso'] !== null ? (float)$row['peso'] : null,
                $row['valor_realizado'] !== null ? (float)$row['valor_realizado'] : null,
                $row['valor_meta'] !== null ? (float)$row['valor_meta'] : null,
                $row['meta_mensal'] !== null ? (float)$row['meta_mensal'] : null,
                $row['canal_venda'] ?? null,
                $row['tipo_venda'] ?? null,
                $row['modalidade_pagamento'] ?? null,
                $dtVencimento,
                $dtCancelamento,
                $row['motivo_cancelamento'] ?? null,
                $row['status_id'] !== null ? (int)$row['status_id'] : null
            );

            $detalhes[] = $dto->toArray();
            
            // Libera memória do DTO após adicionar ao array
            unset($dto);
        }
        
        // Libera memória do result set
        $result->free();

        return $detalhes;
    }

    /**
     * Conta o total de registros que correspondem aos filtros
     * 
     * @param FilterDTO|null $filters Filtros a serem aplicados
     * @return int Total de registros
     */
    public function countDetalhes(?FilterDTO $filters = null): int
    {
        $fRealizadosTable = $this->getTableName(FRealizados::class);
        $dEstruturaTable = $this->getTableName(DEstrutura::class);
        $dProdutosTable = $this->getTableName(DProduto::class);
        $fMetaTable = $this->getTableName(FMeta::class);
        $fDetalhesTable = $this->getTableName(FDetalhes::class);
        $segmentoTable = $this->getTableName(Segmento::class);
        $diretoriaTable = $this->getTableName(Diretoria::class);
        $regionalTable = $this->getTableName(Regional::class);
        $agenciaTable = $this->getTableName(Agencia::class);
        $familiaTable = $this->getTableName(Familia::class);
        $indicadorTable = $this->getTableName(Indicador::class);
        $subindicadorTable = $this->getTableName(Subindicador::class);

        $params = [];
        $whereClause = '';
        
        $params['cargoGerente'] = Cargo::GERENTE;
        $params['cargoGerenteGestao'] = Cargo::GERENTE_GESTAO;

        if ($filters) {
            $gerente = $filters->getGerente();
            $gerenteGestao = $filters->getGerenteGestao();
            $agencia = $filters->getAgencia();
            $regional = $filters->getRegional();
            $diretoria = $filters->getDiretoria();
            $segmento = $filters->getSegmento();

            if ($gerente !== null && $gerente !== '') {
                $whereClause .= " AND fr.funcional = :gerenteFuncional";
                $params['gerenteFuncional'] = $gerente;
            } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
                $dEstruturaTable = $this->getTableName(DEstrutura::class);
                $whereClause .= " AND EXISTS (
                    SELECT 1 FROM {$dEstruturaTable} AS ggestao 
                    WHERE ggestao.funcional = :gerenteGestaoFuncional
                    AND ggestao.cargo_id = :cargoGerenteGestao
                    AND ggestao.segmento_id = est.segmento_id
                    AND ggestao.diretoria_id = est.diretoria_id
                    AND ggestao.regional_id = est.regional_id
                    AND ggestao.agencia_id = est.agencia_id
                )";
                $params['gerenteGestaoFuncional'] = $gerenteGestao;
                $params['cargoGerenteGestao'] = Cargo::GERENTE_GESTAO;
            } else {
                if ($agencia !== null && $agencia !== '') {
                    $whereClause .= " AND est.agencia_id = :agencia";
                    $params['agencia'] = $agencia;
                } elseif ($regional !== null && $regional !== '') {
                    $whereClause .= " AND est.regional_id = :regional";
                    $params['regional'] = $regional;
                } elseif ($diretoria !== null && $diretoria !== '') {
                    $whereClause .= " AND est.diretoria_id = :diretoria";
                    $params['diretoria'] = $diretoria;
                } elseif ($segmento !== null && $segmento !== '') {
                    $whereClause .= " AND est.segmento_id = :segmento";
                    $params['segmento'] = $segmento;
                }
            }

            $familia = $filters->getFamilia();
            $indicador = $filters->getIndicador();
            $subindicador = $filters->getSubindicador();

            if ($subindicador !== null && $subindicador !== '') {
                $whereClause .= " AND prod.subindicador_id = :subindicador";
                $params['subindicador'] = $subindicador;
            } elseif ($indicador !== null && $indicador !== '') {
                $whereClause .= " AND prod.indicador_id = :indicador";
                $params['indicador'] = $indicador;
            } elseif ($familia !== null && $familia !== '') {
                $whereClause .= " AND prod.familia_id = :familia";
                $params['familia'] = $familia;
            }

            $dataInicio = $filters->getDataInicio();
            $dataFim = $filters->getDataFim();

            if ($dataInicio !== null && $dataInicio !== '') {
                $whereClause .= " AND fr.data_realizado >= :dataInicio";
                $params['dataInicio'] = $dataInicio;
            }

            if ($dataFim !== null && $dataFim !== '') {
                $whereClause .= " AND fr.data_realizado <= :dataFim";
                $params['dataFim'] = $dataFim;
            }
        }

        $sql = "SELECT COUNT(DISTINCT fr.id_contrato) as total
                FROM {$fRealizadosTable} AS fr
                INNER JOIN {$dEstruturaTable} AS est ON est.funcional = fr.funcional
                INNER JOIN {$dProdutosTable} AS prod ON prod.id = fr.produto_id
                WHERE 1=1 {$whereClause}";

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeQuery($sql, $params);
        $row = $result->fetchAssociative();
        
        return (int)($row['total'] ?? 0);
    }

    /**
     * Converte ID para funcional se necessário
     * Se o valor for numérico (ID), busca o funcional na tabela d_estrutura
     * Se o valor for string (funcional), retorna o próprio valor
     * 
     * @param mixed $idOrFuncional ID ou funcional
     * @param int $cargoId ID do cargo (GERENTE ou GERENTE_GESTAO)
     * @return string|null Funcional encontrado ou null se não encontrar
     */
    private function getFuncionalFromIdOrFuncional($idOrFuncional, int $cargoId): ?string
    {
        if ($idOrFuncional === null || $idOrFuncional === '') {
            return null;
        }

        // Se não for numérico, assume que já é um funcional
        if (!is_numeric($idOrFuncional)) {
            return (string)$idOrFuncional;
        }

        // Se for numérico, busca o funcional na tabela d_estrutura
        $dEstruturaTable = $this->getTableName(DEstrutura::class);
        $conn = $this->getEntityManager()->getConnection();
        
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

