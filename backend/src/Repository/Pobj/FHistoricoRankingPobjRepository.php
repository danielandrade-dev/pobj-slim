<?php

namespace App\Repository\Pobj;

use App\Domain\DTO\FilterDTO;
use App\Domain\Enum\Cargo;
use App\Entity\Pobj\FPontos;
use App\Entity\Pobj\DEstrutura;
use App\Entity\Pobj\DCalendario;
use App\Entity\Pobj\Segmento;
use App\Entity\Pobj\Diretoria;
use App\Entity\Pobj\Regional;
use App\Entity\Pobj\Agencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FHistoricoRankingPobjRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FPontos::class);
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
     * Lista todo o histórico ordenado por ranking (agora usando f_pontos)
     */
    public function findAllOrderedByRanking(): array
    {
        $fPontosTable = $this->getTableName(FPontos::class);
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "SELECT 
                    funcional,
                    SUM(realizado) as total_pontos
                FROM {$fPontosTable}
                GROUP BY funcional
                ORDER BY total_pontos DESC";
        
        $result = $conn->executeQuery($sql);
        return $result->fetchAllAssociative();
    }

    /**
     * Busca dados de ranking com filtros e informações de estrutura
     * Agora usa f_pontos ao invés de f_historico_ranking_pobj
     */
    public function findRankingWithFilters(?FilterDTO $filters = null): array
    {
        // Primeiro, verifica se há dados na tabela
        $fPontosTable = $this->getTableName(FPontos::class);
        $conn = $this->getEntityManager()->getConnection();
        $countResult = $conn->executeQuery("SELECT COUNT(*) as total FROM {$fPontosTable}");
        $count = $countResult->fetchOne();
        
        // Se não houver dados, retorna vazio
        if ($count == 0) {
            return [];
        }
        
        $fPontosTable = $this->getTableName(FPontos::class);
        $estruturaTable = $this->getTableName(DEstrutura::class);
        $calendarioTable = $this->getTableName(DCalendario::class);
        $segmentoTable = $this->getTableName(Segmento::class);
        $diretoriaTable = $this->getTableName(Diretoria::class);
        $regionalTable = $this->getTableName(Regional::class);
        $agenciaTable = $this->getTableName(Agencia::class);

        // Adiciona parâmetros de cargo primeiro
        $params = [
            'cargoGerente' => Cargo::GERENTE,
            'cargoGerenteGestao' => Cargo::GERENTE_GESTAO
        ];
        $whereClause = '';

        if ($filters) {
            // Filtros de estrutura (aplica apenas o mais específico da hierarquia)
            $gerente = $filters->getGerente();
            $gerenteGestao = $filters->getGerenteGestao();
            $agencia = $filters->getAgencia();
            $regional = $filters->getRegional();
            $diretoria = $filters->getDiretoria();
            $segmento = $filters->getSegmento();

            // Se tiver gerente, filtra apenas por funcional
            if ($gerente !== null && $gerente !== '') {
                $whereClause .= " AND est.funcional = :gerenteFuncional";
                $params['gerenteFuncional'] = $gerente;
            } elseif ($gerenteGestao !== null && $gerenteGestao !== '') {
                // Se tiver gerente gestão, filtra por todos os gerentes da mesma estrutura
                $whereClause .= " AND EXISTS (
                    SELECT 1 FROM {$estruturaTable} AS ggestao 
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
                // Aplica apenas o filtro mais específico da hierarquia de estrutura
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

            // Filtros de data
            $dataInicio = $filters->getDataInicio();
            $dataFim = $filters->getDataFim();

            if ($dataInicio !== null && $dataInicio !== '') {
                $whereClause .= " AND p.data_realizado >= :dataInicio";
                $params['dataInicio'] = $dataInicio;
            }

            if ($dataFim !== null && $dataFim !== '') {
                $whereClause .= " AND p.data_realizado <= :dataFim";
                $params['dataFim'] = $dataFim;
            }
        }

        // Primeiro, cria uma subquery com os dados agregados
        $subquery = "SELECT
                    MAX(p.data_realizado) AS data,
                    DATE_FORMAT(MAX(p.data_realizado), '%Y-%m') AS competencia,
                    CAST(seg.id AS CHAR) AS segmento_id,
                    seg.nome AS segmento,
                    CAST(dir.id AS CHAR) AS diretoria_id,
                    dir.nome AS diretoria_nome,
                    CAST(reg.id AS CHAR) AS gerencia_id,
                    reg.nome AS gerencia_nome,
                    CAST(ag.id AS CHAR) AS agencia_id,
                    ag.nome AS agencia_nome,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN est.funcional
                        WHEN est.cargo_id = :cargoGerenteGestao THEN NULL
                        ELSE NULL
                    END AS gerente_id,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN est.nome
                        WHEN est.cargo_id = :cargoGerenteGestao THEN NULL
                        ELSE NULL
                    END AS gerente_nome,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN ggestao.funcional
                        WHEN est.cargo_id = :cargoGerenteGestao THEN est.funcional
                        ELSE NULL
                    END AS gerente_gestao_id,
                    CASE 
                        WHEN est.cargo_id = :cargoGerente THEN ggestao.nome
                        WHEN est.cargo_id = :cargoGerenteGestao THEN est.nome
                        ELSE NULL
                    END AS gerente_gestao_nome,
                    SUM(p.realizado) AS realizado_mensal,
                    SUM(p.meta) AS meta_mensal,
                    SUM(p.realizado) AS pontos
                FROM {$fPontosTable} AS p
                INNER JOIN {$estruturaTable} AS est
                    ON est.funcional = p.funcional
                LEFT JOIN {$segmentoTable} AS seg
                    ON seg.id = est.segmento_id
                LEFT JOIN {$diretoriaTable} AS dir
                    ON dir.id = est.diretoria_id
                LEFT JOIN {$regionalTable} AS reg
                    ON reg.id = est.regional_id
                LEFT JOIN {$agenciaTable} AS ag
                    ON ag.id = est.agencia_id
                LEFT JOIN (
                    SELECT 
                        g1.agencia_id,
                        g1.funcional,
                        g1.nome
                    FROM {$estruturaTable} AS g1
                    INNER JOIN (
                        SELECT agencia_id, MIN(id) AS min_id
                        FROM {$estruturaTable}
                        WHERE cargo_id = :cargoGerenteGestao
                        AND agencia_id IS NOT NULL
                        GROUP BY agencia_id
                    ) AS g2 ON g1.id = g2.min_id AND g1.agencia_id = g2.agencia_id
                    WHERE g1.cargo_id = :cargoGerenteGestao
                ) AS ggestao
                    ON ggestao.agencia_id = est.agencia_id
                WHERE 1=1 {$whereClause}
                GROUP BY est.funcional, seg.id, dir.id, reg.id, ag.id, est.cargo_id, ggestao.funcional, ggestao.nome";

        // Query principal com ranking calculado baseado no realizado_mensal
        // Usa variáveis MySQL para calcular o ranking considerando empates
        $sql = "SELECT 
                    ranked.*,
                    @rank := CASE 
                        WHEN @prev_realizado = ranked.realizado_mensal THEN @rank
                        ELSE @rank + 1
                    END AS ranking_mensal,
                    @prev_realizado := ranked.realizado_mensal AS _prev
                FROM (
                    {$subquery}
                ) AS ranked
                CROSS JOIN (SELECT @rank := 0, @prev_realizado := NULL) AS r
                ORDER BY ranked.realizado_mensal DESC, ranked.pontos DESC";

        $conn = $this->getEntityManager()->getConnection();
        $result = $conn->executeQuery($sql, $params);
        
        // Processa resultados de forma mais eficiente em memória
        $formatted = [];
        while ($row = $result->fetchAssociative()) {
            $formatted[] = [
                'data' => $row['data'] ? (new \DateTime($row['data']))->format('Y-m-d') : null,
                'competencia' => $row['competencia'] ?? null,
                'segmento_id' => $row['segmento_id'] ?? null,
                'segmento' => $row['segmento'] ?? null,
                'diretoria_id' => $row['diretoria_id'] ?? null,
                'diretoria_nome' => $row['diretoria_nome'] ?? null,
                'gerencia_id' => $row['gerencia_id'] ?? null,
                'gerencia_nome' => $row['gerencia_nome'] ?? null,
                'agencia_id' => $row['agencia_id'] ?? null,
                'agencia_nome' => $row['agencia_nome'] ?? null,
                'gerente_gestao_id' => $row['gerente_gestao_id'] ?? null,
                'gerente_gestao_nome' => $row['gerente_gestao_nome'] ?? null,
                'gerente_id' => $row['gerente_id'] ?? null,
                'gerente_nome' => $row['gerente_nome'] ?? null,
                'rank' => $row['ranking_mensal'] !== null ? (int)$row['ranking_mensal'] : null, // Ranking baseado no realizado_mensal
                'realizado_mensal' => $row['realizado_mensal'] !== null ? (float)$row['realizado_mensal'] : null,
                'meta_mensal' => $row['meta_mensal'] !== null ? (float)$row['meta_mensal'] : null,
                'pontos' => $row['pontos'] !== null ? (float)$row['pontos'] : null,
            ];
        }
        $result->free();

        return $formatted;
    }
}

