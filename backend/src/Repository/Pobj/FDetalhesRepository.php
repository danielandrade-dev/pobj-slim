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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FDetalhesRepository extends ServiceEntityRepository
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
        $whereClause = '';

        if ($filters) {
            // Filtros de estrutura
            $segmento = $filters->getSegmento();
            $diretoria = $filters->getDiretoria();
            $regional = $filters->getRegional();
            $agencia = $filters->getAgencia();
            $gerente = $filters->getGerente();

            if ($segmento !== null && $segmento !== '') {
                $whereClause .= " AND est.segmento_id = :segmento";
                $params['segmento'] = $segmento;
            }

            if ($diretoria !== null && $diretoria !== '') {
                $whereClause .= " AND est.diretoria_id = :diretoria";
                $params['diretoria'] = $diretoria;
            }

            if ($regional !== null && $regional !== '') {
                $whereClause .= " AND est.regional_id = :regional";
                $params['regional'] = $regional;
            }

            if ($agencia !== null && $agencia !== '') {
                $whereClause .= " AND est.agencia_id = :agencia";
                $params['agencia'] = $agencia;
            }

            if ($gerente !== null && $gerente !== '') {
                $whereClause .= " AND fr.funcional = :gerente";
                $params['gerente'] = $gerente;
            }

            // Filtros de produto
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

            // Filtros de data
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

        $sql = "SELECT
                    fr.id_contrato,
                    fr.id_contrato AS registro_id,
                    fr.data_realizado AS data,
                    fr.data_realizado AS competencia,
                    cal.ano,
                    cal.mes,
                    cal.mes_nome,
                    est.segmento_id,
                    seg.nome AS segmento,
                    est.diretoria_id,
                    dir.nome AS diretoria_nome,
                    est.regional_id AS gerencia_id,
                    reg.nome AS gerencia_nome,
                    est.agencia_id,
                    ag.nome AS agencia_nome,
                    fr.funcional AS gerente_id,
                    est.nome AS gerente_nome,
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
                FROM {$fRealizadosTable} AS fr
                JOIN {$dCalendarioTable} AS cal
                    ON cal.data = fr.data_realizado
                JOIN {$dEstruturaTable} AS est
                    ON est.funcional = fr.funcional
                JOIN {$dProdutosTable} AS prod
                    ON prod.id = fr.produto_id
                LEFT JOIN {$segmentoTable} AS seg
                    ON seg.id = est.segmento_id
                LEFT JOIN {$diretoriaTable} AS dir
                    ON dir.id = est.diretoria_id
                LEFT JOIN {$regionalTable} AS reg
                    ON reg.id = est.regional_id
                LEFT JOIN {$agenciaTable} AS ag
                    ON ag.id = est.agencia_id
                LEFT JOIN {$familiaTable} AS fam
                    ON fam.id = prod.familia_id
                LEFT JOIN {$indicadorTable} AS ind
                    ON ind.id = prod.indicador_id
                LEFT JOIN {$subindicadorTable} AS sub
                    ON sub.id = prod.subindicador_id
                LEFT JOIN {$fMetaTable} AS meta
                    ON meta.funcional = fr.funcional
                    AND meta.produto_id = fr.produto_id
                    AND YEAR(meta.data_meta) = cal.ano
                    AND MONTH(meta.data_meta) = cal.mes
                LEFT JOIN {$fDetalhesTable} AS det
                    ON det.contrato_id = fr.id_contrato
                WHERE 1=1 {$whereClause}
                ORDER BY est.diretoria_id, est.regional_id, est.agencia_id, est.nome, prod.familia_id, prod.indicador_id, prod.subindicador_id, fr.id_contrato";

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeQuery($sql, $params);
        $rows = $result->fetchAllAssociative();

        // Formata os dados
        $detalhes = [];
        foreach ($rows as $row) {
            // Formata datas
            $data = $row['data'] ?? null;
            if ($data instanceof \DateTimeInterface) {
                $data = $data->format('Y-m-d');
            } elseif (is_string($data)) {
                // Já está no formato correto
            } else {
                $data = null;
            }

            $competencia = $row['competencia'] ?? null;
            if ($competencia instanceof \DateTimeInterface) {
                $competencia = $competencia->format('Y-m-d');
            } elseif (is_string($competencia)) {
                // Já está no formato correto
            } else {
                $competencia = null;
            }

            $dtVencimento = $row['dt_vencimento'] ?? null;
            if ($dtVencimento instanceof \DateTimeInterface) {
                $dtVencimento = $dtVencimento->format('Y-m-d');
            } elseif (is_string($dtVencimento)) {
                // Já está no formato correto
            } else {
                $dtVencimento = null;
            }

            $dtCancelamento = $row['dt_cancelamento'] ?? null;
            if ($dtCancelamento instanceof \DateTimeInterface) {
                $dtCancelamento = $dtCancelamento->format('Y-m-d');
            } elseif (is_string($dtCancelamento)) {
                // Já está no formato correto
            } else {
                $dtCancelamento = null;
            }

            $detalhes[] = [
                'registro_id' => $row['registro_id'] ?? $row['id_contrato'] ?? null,
                'id_contrato' => $row['id_contrato'] ?? null,
                'data' => $data,
                'competencia' => $competencia,
                'ano' => $row['ano'] ?? null,
                'mes' => $row['mes'] ?? null,
                'mes_nome' => $row['mes_nome'] ?? null,
                'segmento_id' => $row['segmento_id'] ? (string)$row['segmento_id'] : null,
                'segmento' => $row['segmento'] ?? null,
                'diretoria_id' => $row['diretoria_id'] ? (string)$row['diretoria_id'] : null,
                'diretoria_nome' => $row['diretoria_nome'] ?? null,
                'gerencia_id' => $row['gerencia_id'] ? (string)$row['gerencia_id'] : null,
                'gerencia_nome' => $row['gerencia_nome'] ?? null,
                'agencia_id' => $row['agencia_id'] ? (string)$row['agencia_id'] : null,
                'agencia_nome' => $row['agencia_nome'] ?? null,
                'gerente_id' => $row['gerente_id'] ?? null,
                'gerente_nome' => $row['gerente_nome'] ?? null,
                'familia_id' => $row['familia_id'] ? (string)$row['familia_id'] : null,
                'familia_nome' => $row['familia_nome'] ?? null,
                'id_indicador' => $row['id_indicador'] ? (string)$row['id_indicador'] : null,
                'ds_indicador' => $row['ds_indicador'] ?? null,
                'id_subindicador' => $row['id_subindicador'] ? (string)$row['id_subindicador'] : null,
                'subindicador' => $row['subindicador'] ?? null,
                'peso' => $row['peso'] !== null ? (float)$row['peso'] : null,
                'valor_realizado' => $row['valor_realizado'] !== null ? (float)$row['valor_realizado'] : null,
                'valor_meta' => $row['valor_meta'] !== null ? (float)$row['valor_meta'] : null,
                'meta_mensal' => $row['meta_mensal'] !== null ? (float)$row['meta_mensal'] : null,
                'canal_venda' => $row['canal_venda'] ?? null,
                'tipo_venda' => $row['tipo_venda'] ?? null,
                'modalidade_pagamento' => $row['modalidade_pagamento'] ?? null,
                'dt_vencimento' => $dtVencimento,
                'dt_cancelamento' => $dtCancelamento,
                'motivo_cancelamento' => $row['motivo_cancelamento'] ?? null,
                'status_id' => $row['status_id'] !== null ? (int)$row['status_id'] : null,
            ];
        }

        return $detalhes;
    }
}

