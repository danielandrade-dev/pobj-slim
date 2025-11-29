<?php

namespace App\Application\UseCase\Pobj;

use App\Domain\DTO\FilterDTO;
use App\Repository\Pobj\DProdutoRepository;
use App\Domain\Enum\Tables;
use App\Infrastructure\Database\Connection;

class ProdutoUseCase
{
    private $repository;

    public function __construct(DProdutoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna produtos com dados agregados (realizados, metas, pontos, variável)
     * para renderização dos cards
     */
    public function handle(FilterDTO $filters = null): array
    {
        // Busca produtos base
        $produtos = $this->repository->findAllOrdered();
        
        if (empty($produtos)) {
            return [];
        }

        // Extrai IDs de produtos (d_produtos.id)
        $produtoIds = array_unique(array_filter(array_map(function($produto) {
            return $produto['id'] ?? null;
        }, $produtos)));

        if (empty($produtoIds)) {
            return $this->formatProdutosForCards($produtos);
        }

        // Busca dados agregados usando produto_id
        $realizados = $this->getRealizadosAgregados($produtoIds, $filters);
        $metas = $this->getMetasAgregadas($produtoIds, $filters);
        $pontos = $this->getPontosAgregados($produtoIds, $filters);
        $variavel = $this->getVariavelAgregada($produtoIds, $filters);

        // Combina dados
        return $this->combineProdutosData($produtos, $realizados, $metas, $pontos, $variavel);
    }

    /**
     * Busca realizados agregados por produto
     */
    private function getRealizadosAgregados(array $produtoIds, ?FilterDTO $filters): array
    {
        if (empty($produtoIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $sql = "SELECT 
                    r.produto_id,
                    SUM(r.realizado) as realizado_total,
                    MAX(r.data_realizado) as ultima_atualizacao
                FROM " . Tables::F_REALIZADOS . " as r
                WHERE r.produto_id IN ($placeholders)
                AND r.produto_id IS NOT NULL";
        
        $params = $produtoIds;
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $sql .= " AND r.data_realizado >= ?";
            $params[] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND r.data_realizado <= ?";
            $params[] = $dataFim;
        }
        
        $sql .= " GROUP BY r.produto_id";
        
        $connection = Connection::getInstance();
        $rows = $connection->select($sql, $params);
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row['produto_id'];
            $result[$produtoId] = [
                'realizado' => (float)($row['realizado_total'] ?? 0),
                'ultima_atualizacao' => $row['ultima_atualizacao'] ?? null
            ];
        }
        
        return $result;
    }

    /**
     * Busca metas agregadas por produto
     */
    private function getMetasAgregadas(array $produtoIds, ?FilterDTO $filters): array
    {
        if (empty($produtoIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $sql = "SELECT 
                    m.produto_id,
                    SUM(m.meta_mensal) as meta_total
                FROM " . Tables::F_META . " as m
                WHERE m.produto_id IN ($placeholders)";
        
        $params = $produtoIds;
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $sql .= " AND m.data_meta >= ?";
            $params[] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND m.data_meta <= ?";
            $params[] = $dataFim;
        }
        
        $sql .= " GROUP BY m.produto_id";
        
        $connection = Connection::getInstance();
        $rows = $connection->select($sql, $params);
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row['produto_id'];
            $result[$produtoId] = (float)($row['meta_total'] ?? 0);
        }
        
        return $result;
    }

    /**
     * Busca pontos agregados por produto
     */
    private function getPontosAgregados(array $produtoIds, ?FilterDTO $filters): array
    {
        if (empty($produtoIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $sql = "SELECT 
                    p.produto_id,
                    SUM(p.realizado) as pontos_realizado,
                    SUM(p.meta) as pontos_meta
                FROM " . Tables::F_PONTOS . " as p
                WHERE p.produto_id IN ($placeholders)";
        
        $params = $produtoIds;
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $sql .= " AND p.data_realizado >= ?";
            $params[] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND p.data_realizado <= ?";
            $params[] = $dataFim;
        }
        
        $sql .= " GROUP BY p.produto_id";
        
        $connection = Connection::getInstance();
        $rows = $connection->select($sql, $params);
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row['produto_id'];
            $result[$produtoId] = [
                'pontos' => (float)($row['pontos_realizado'] ?? 0),
                'pontos_meta' => (float)($row['pontos_meta'] ?? 0)
            ];
        }
        
        return $result;
    }

    /**
     * Busca variável agregada por produto
     * Nota: f_variavel não tem relação direta com produtos, então retorna vazio por enquanto
     */
    private function getVariavelAgregada(array $produtoIds, ?FilterDTO $filters): array
    {
        // A tabela f_variavel não tem relação direta com produtos/indicadores
        // Por enquanto retorna vazio, pode ser implementado depois se necessário
        return [];
    }

    /**
     * Combina dados de produtos com dados agregados
     */
    private function combineProdutosData(
        array $produtos,
        array $realizados,
        array $metas,
        array $pontos,
        array $variavel
    ): array {
        $result = [];
        
        foreach ($produtos as $produto) {
            $produtoId = (string)($produto['id'] ?? '');
            
            $realizadoData = $realizados[$produtoId] ?? null;
            $metaTotal = $metas[$produtoId] ?? 0;
            $pontosData = $pontos[$produtoId] ?? null;
            $variavelData = $variavel[$produtoId] ?? null;
            
            $realizadoTotal = $realizadoData['realizado'] ?? 0;
            $pontosRealizado = $pontosData['pontos'] ?? 0;
            $pontosMeta = $pontosData['pontos_meta'] ?? ($produto['peso'] ?? 0);
            
            // Calcula atingimento
            $ating = $metaTotal > 0 ? ($realizadoTotal / $metaTotal) : 0;
            $atingido = $ating >= 1 || ($pontosMeta > 0 && ($pontosRealizado / $pontosMeta) >= 1);
            
            $result[] = [
                'id' => $produtoId,
                'id_indicador' => (string)($produto['id_indicador'] ?? ''),
                'indicador' => $produto['indicador'] ?? '',
                'id_familia' => (string)($produto['id_familia'] ?? ''),
                'familia' => $produto['familia'] ?? '',
                'id_subindicador' => $produto['id_subindicador'] ? (string)$produto['id_subindicador'] : null,
                'subindicador' => $produto['subindicador'] ?? null,
                'metrica' => $produto['metrica'] ?? 'valor',
                'peso' => (float)($produto['peso'] ?? 0),
                'meta' => $metaTotal,
                'realizado' => $realizadoTotal,
                'pontos' => $pontosRealizado,
                'pontos_meta' => $pontosMeta,
                'variavel_meta' => $variavelData['variavel_meta'] ?? 0,
                'variavel_realizado' => $variavelData['variavel_realizado'] ?? 0,
                'ating' => $ating,
                'atingido' => $atingido,
                'ultima_atualizacao' => $realizadoData['ultima_atualizacao'] ?? null
            ];
        }
        
        return $result;
    }

    /**
     * Formata produtos para cards quando não há dados agregados
     */
    private function formatProdutosForCards(array $produtos): array
    {
        return array_map(function($produto) {
            return [
                'id' => (string)($produto['id'] ?? ''),
                'id_indicador' => (string)($produto['id_indicador'] ?? ''),
                'indicador' => $produto['indicador'] ?? '',
                'id_familia' => (string)($produto['id_familia'] ?? ''),
                'familia' => $produto['familia'] ?? '',
                'id_subindicador' => $produto['id_subindicador'] ? (string)$produto['id_subindicador'] : null,
                'subindicador' => $produto['subindicador'] ?? null,
                'metrica' => $produto['metrica'] ?? 'valor',
                'peso' => (float)($produto['peso'] ?? 0),
                'meta' => 0,
                'realizado' => 0,
                'pontos' => 0,
                'pontos_meta' => (float)($produto['peso'] ?? 0),
                'variavel_meta' => 0,
                'variavel_realizado' => 0,
                'ating' => 0,
                'atingido' => false,
                'ultima_atualizacao' => null
            ];
        }, $produtos);
    }

    /**
     * Retorna produtos com dados mensais para renderização do resumo-legacy
     * Retorna dados agrupados por mês (competencia/data_realizado)
     */
    public function handleMonthly(FilterDTO $filters = null): array
    {
        // Busca produtos base
        $produtos = $this->repository->fetch($filters);
        
        if (empty($produtos)) {
            return [];
        }

        // Extrai IDs de produtos
        $produtoIds = array_unique(array_filter(array_map(function($produto) {
            return $produto['id'] ?? null;
        }, $produtos)));

        if (empty($produtoIds)) {
            return [];
        }

        // Busca dados mensais
        $realizadosMensais = $this->getRealizadosMensais($produtoIds, $filters);
        $metasMensais = $this->getMetasMensais($produtoIds, $filters);

        // Combina dados mensais com produtos
        return $this->combineProdutosDataMonthly($produtos, $realizadosMensais, $metasMensais);
    }

    /**
     * Busca realizados mensais por produto e mês
     */
    private function getRealizadosMensais(array $produtoIds, ?FilterDTO $filters): array
    {
        if (empty($produtoIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $sql = "SELECT 
                    r.produto_id,
                    DATE_FORMAT(r.data_realizado, '%Y-%m') as mes,
                    SUM(r.realizado) as realizado_total,
                    MAX(r.data_realizado) as ultima_atualizacao
                FROM " . Tables::F_REALIZADOS . " as r
                WHERE r.produto_id IN ($placeholders)
                AND r.produto_id IS NOT NULL";
        
        $params = $produtoIds;
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $sql .= " AND r.data_realizado >= ?";
            $params[] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND r.data_realizado <= ?";
            $params[] = $dataFim;
        }
        
        $sql .= " GROUP BY r.produto_id, DATE_FORMAT(r.data_realizado, '%Y-%m')";
        
        $connection = Connection::getInstance();
        $rows = $connection->select($sql, $params);
        
        $result = [];
        $ultimaAtualizacaoMap = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row['produto_id'];
            $mes = $row['mes'] ?? '';
            if (!isset($result[$produtoId])) {
                $result[$produtoId] = [];
            }
            $result[$produtoId][$mes] = (float)($row['realizado_total'] ?? 0);
            
            // Armazena a última atualização mais recente por produto
            if ($row['ultima_atualizacao']) {
                if (!isset($ultimaAtualizacaoMap[$produtoId]) || 
                    $row['ultima_atualizacao'] > $ultimaAtualizacaoMap[$produtoId]) {
                    $ultimaAtualizacaoMap[$produtoId] = $row['ultima_atualizacao'];
                }
            }
        }
        
        // Adiciona última atualização ao resultado
        foreach ($ultimaAtualizacaoMap as $produtoId => $ultimaAtualizacao) {
            if (!isset($result[$produtoId]['_ultima_atualizacao'])) {
                $result[$produtoId]['_ultima_atualizacao'] = $ultimaAtualizacao;
            }
        }
        
        return $result;
    }

    /**
     * Busca metas mensais por produto e mês
     */
    private function getMetasMensais(array $produtoIds, ?FilterDTO $filters): array
    {
        if (empty($produtoIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $sql = "SELECT 
                    m.produto_id,
                    DATE_FORMAT(m.data_meta, '%Y-%m') as mes,
                    SUM(m.meta_mensal) as meta_total
                FROM " . Tables::F_META . " as m
                WHERE m.produto_id IN ($placeholders)";
        
        $params = $produtoIds;
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $sql .= " AND m.data_meta >= ?";
            $params[] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND m.data_meta <= ?";
            $params[] = $dataFim;
        }
        
        $sql .= " GROUP BY m.produto_id, DATE_FORMAT(m.data_meta, '%Y-%m')";
        
        $connection = Connection::getInstance();
        $rows = $connection->select($sql, $params);
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row['produto_id'];
            $mes = $row['mes'] ?? '';
            if (!isset($result[$produtoId])) {
                $result[$produtoId] = [];
            }
            $result[$produtoId][$mes] = (float)($row['meta_total'] ?? 0);
        }
        
        return $result;
    }

    /**
     * Combina dados de produtos com dados mensais
     */
    private function combineProdutosDataMonthly(
        array $produtos,
        array $realizadosMensais,
        array $metasMensais
    ): array {
        $result = [];
        
        foreach ($produtos as $produto) {
            $produtoId = (string)($produto['id'] ?? '');
            
            $realizadosMes = $realizadosMensais[$produtoId] ?? [];
            $metasMes = $metasMensais[$produtoId] ?? [];
            
            // Remove a chave especial de última atualização
            $ultimaAtualizacao = $realizadosMes['_ultima_atualizacao'] ?? null;
            unset($realizadosMes['_ultima_atualizacao']);
            
            // Calcula totais
            $realizadoTotal = array_sum($realizadosMes);
            $metaTotal = array_sum($metasMes);
            $ating = $metaTotal > 0 ? ($realizadoTotal / $metaTotal) : 0;
            
            // Prepara dados mensais
            $meses = array_unique(array_merge(array_keys($realizadosMes), array_keys($metasMes)));
            $dadosMensais = [];
            
            foreach ($meses as $mes) {
                $meta = $metasMes[$mes] ?? 0;
                $realizado = $realizadosMes[$mes] ?? 0;
                $atingMes = $meta > 0 ? ($realizado / $meta) : 0;
                
                $dadosMensais[] = [
                    'mes' => $mes,
                    'meta' => $meta,
                    'realizado' => $realizado,
                    'atingimento' => $atingMes * 100 // Em percentual
                ];
            }
            
            $result[] = [
                'id' => $produtoId,
                'id_indicador' => (string)($produto['id_indicador'] ?? ''),
                'indicador' => $produto['indicador'] ?? '',
                'id_familia' => (string)($produto['id_familia'] ?? ''),
                'familia' => $produto['familia'] ?? '',
                'id_subindicador' => $produto['id_subindicador'] ? (string)$produto['id_subindicador'] : null,
                'subindicador' => $produto['subindicador'] ?? null,
                'metrica' => $produto['metrica'] ?? 'valor',
                'peso' => (float)($produto['peso'] ?? 0),
                'meta' => $metaTotal,
                'realizado' => $realizadoTotal,
                'ating' => $ating,
                'atingido' => $ating >= 1,
                'ultima_atualizacao' => $ultimaAtualizacao,
                'meses' => $dadosMensais
            ];
        }
        
        return $result;
    }
}

