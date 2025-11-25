<?php

namespace App\Application\UseCase;

use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\ProdutoRepository;
use App\Domain\Enum\Tables;
use Illuminate\Database\Capsule\Manager as DB;

class ProdutoUseCase extends AbstractUseCase
{
    public function __construct(ProdutoRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Retorna produtos com dados agregados (realizados, metas, pontos, variável)
     * para renderização dos cards
     */
    public function handle(FilterDTO $filters = null): array
    {
        // Busca produtos base
        $produtos = $this->repository->fetch($filters);
        
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
        
        // f_realizados tem produto_id que referencia d_produtos.id
        $query = DB::table(Tables::F_REALIZADOS . ' as r')
            ->select(
                'r.produto_id',
                DB::raw('SUM(r.realizado) as realizado_total'),
                DB::raw('MAX(r.data_realizado) as ultima_atualizacao')
            )
            ->whereIn('r.produto_id', $produtoIds)
            ->whereNotNull('r.produto_id');
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $query->where('r.data_realizado', '>=', $dataInicio);
        }
        
        if ($dataFim) {
            $query->where('r.data_realizado', '<=', $dataFim);
        }
        
        $query->groupBy('r.produto_id');
        
        $rows = $query->get();
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row->produto_id;
            $result[$produtoId] = [
                'realizado' => (float)($row->realizado_total ?? 0),
                'ultima_atualizacao' => $row->ultima_atualizacao ?? null
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
        
        $query = DB::table(Tables::F_META . ' as m')
            ->select(
                'm.produto_id',
                DB::raw('SUM(m.meta_mensal) as meta_total')
            )
            ->whereIn('m.produto_id', $produtoIds);
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $query->where('m.data_meta', '>=', $dataInicio);
        }
        
        if ($dataFim) {
            $query->where('m.data_meta', '<=', $dataFim);
        }
        
        $query->groupBy('m.produto_id');
        
        $rows = $query->get();
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row->produto_id;
            $result[$produtoId] = (float)($row->meta_total ?? 0);
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
        
        $query = DB::table(Tables::F_PONTOS . ' as p')
            ->select(
                'p.produto_id',
                DB::raw('SUM(p.realizado) as pontos_realizado'),
                DB::raw('SUM(p.meta) as pontos_meta')
            )
            ->whereIn('p.produto_id', $produtoIds);
        
        // Aplica filtros de período se existirem
        $dataInicio = $filters ? $filters->getDataInicio() : null;
        $dataFim = $filters ? $filters->getDataFim() : null;
        
        if ($dataInicio) {
            $query->where('p.data_realizado', '>=', $dataInicio);
        }
        
        if ($dataFim) {
            $query->where('p.data_realizado', '<=', $dataFim);
        }
        
        $query->groupBy('p.produto_id');
        
        $rows = $query->get();
        
        $result = [];
        foreach ($rows as $row) {
            $produtoId = (string)$row->produto_id;
            $result[$produtoId] = [
                'pontos' => (float)($row->pontos_realizado ?? 0),
                'pontos_meta' => (float)($row->pontos_meta ?? 0)
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
}

