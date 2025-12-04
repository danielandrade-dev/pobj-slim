<?php

namespace App\Application\UseCase\Pobj;

use App\Domain\DTO\FilterDTO;
use App\Repository\Pobj\FHistoricoRankingPobjRepository;

class RankingUseCase
{
    private $repository;

    public function __construct(FHistoricoRankingPobjRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(?FilterDTO $filters = null): array
    {
        $rawData = $this->repository->findRankingWithFilters($filters);
        
        if (empty($rawData)) {
            return [];
        }

        // Obtém o nível de agrupamento (padrão: gerenteGestao)
        $nivel = $filters ? $filters->get('nivel', 'gerenteGestao') : 'gerenteGestao';
        
        // Agrupa e processa os dados
        return $this->processRankingData($rawData, $nivel, $filters);
    }

    /**
     * Processa dados de ranking: agrupa por nível e aplica regras de exibição
     */
    private function processRankingData(array $rawData, string $nivel, ?FilterDTO $filters): array
    {
        // Mapeia nível para campos
        $levelFields = [
            'segmento' => ['key' => 'segmento_id', 'label' => 'segmento'],
            'diretoria' => ['key' => 'diretoria_id', 'label' => 'diretoria_nome'],
            'gerencia' => ['key' => 'gerencia_id', 'label' => 'gerencia_nome'],
            'agencia' => ['key' => 'agencia_id', 'label' => 'agencia_nome'],
            'gerenteGestao' => ['key' => 'gerente_gestao_id', 'label' => 'gerente_gestao_nome'],
            'gerente' => ['key' => 'gerente_id', 'label' => 'gerente_nome'],
        ];

        $fieldConfig = $levelFields[$nivel] ?? $levelFields['gerenteGestao'];
        $keyField = $fieldConfig['key'];
        $labelField = $fieldConfig['label'];

        // Obtém seleção atual do nível (se houver filtro aplicado)
        $selectionForLevel = $this->getSelectionForLevel($nivel, $filters);
        $hasSelection = $selectionForLevel !== null && !$this->isDefaultSelection($selectionForLevel);

        // Agrupa dados por nível
        $groups = [];
        foreach ($rawData as $item) {
            $key = $item[$keyField] ?? 'unknown';
            $label = $item[$labelField] ?? $key ?? '—';
            
            // Para gerenteGestao, também armazena o ID numérico para comparação
            $idNum = null;
            if ($nivel === 'gerenteGestao' && isset($item['gerente_gestao_id_num'])) {
                $idNum = $item['gerente_gestao_id_num'];
            } elseif ($nivel === 'gerente' && isset($item['gerente_id'])) {
                // Para gerente, o ID pode estar em outro campo se necessário
                $idNum = $item['gerente_id'];
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'unidade' => $key,
                    'label' => $label,
                    'id_num' => $idNum, // ID numérico para comparação
                    'pontos' => 0,
                    'count' => 0,
                ];
            }

            // Soma pontos (usando pontos ou realizado_mensal como fallback)
            $pontos = $item['pontos'] ?? $item['realizado_mensal'] ?? 0;
            $groups[$key]['pontos'] += $pontos;
            $groups[$key]['count'] += 1;
        }

        // Converte para array e ordena por pontos
        $grouped = array_values($groups);
        usort($grouped, function($a, $b) {
            return $b['pontos'] <=> $a['pontos'];
        });

        // Aplica regras de exibição (mascaramento)
        $result = [];
        foreach ($grouped as $index => $item) {
            $shouldMask = $this->shouldMaskItem($item, $index, $hasSelection, $selectionForLevel, $nivel);
            
            $result[] = [
                'unidade' => $item['unidade'],
                'label' => $item['label'],
                'displayLabel' => $shouldMask ? '*****' : $item['label'],
                'pontos' => $item['pontos'],
                'count' => $item['count'],
                'position' => $index + 1,
            ];
        }

        return $result;
    }

    /**
     * Determina se um item deve ser mascarado baseado nas regras de negócio
     */
    private function shouldMaskItem(array $item, int $index, bool $hasSelection, ?string $selectionForLevel, string $nivel): bool
    {
        // Se não há filtro aplicado, mascara todos exceto o primeiro
        if (!$hasSelection) {
            return $index !== 0;
        }

        // Se há filtro aplicado, mostra apenas o item que corresponde ao filtro
        if ($hasSelection && $selectionForLevel) {
            // Compara com unidade (funcional), label (nome) e id_num (ID numérico)
            $idNum = $item['id_num'] ?? null;
            $matches = $this->matchesSelection($selectionForLevel, $item['unidade'], $item['label'], $idNum);
            return !$matches; // Mascara se não corresponder ao filtro
        }

        return true;
    }

    /**
     * Obtém a seleção atual para um nível específico
     */
    private function getSelectionForLevel(string $nivel, ?FilterDTO $filters): ?string
    {
        if (!$filters) {
            return null;
        }

        $levelMap = [
            'segmento' => 'segmento',
            'diretoria' => 'diretoria',
            'gerencia' => 'regional',
            'agencia' => 'agencia',
            'gerenteGestao' => 'gerenteGestao',
            'gerente' => 'gerente',
        ];

        $filterKey = $levelMap[$nivel] ?? null;
        if (!$filterKey) {
            return null;
        }

        return $filters->get($filterKey);
    }

    /**
     * Verifica se uma seleção é padrão (todos/todas)
     */
    private function isDefaultSelection(?string $value): bool
    {
        if (!$value) {
            return true;
        }
        $normalized = strtolower(trim($value));
        return in_array($normalized, ['todos', 'todas', '']);
    }

    /**
     * Verifica se um valor corresponde à seleção
     */
    private function matchesSelection(string $filterValue, ?string $candidate1, ?string $candidate2, ?string $candidateIdNum = null): bool
    {
        if ($this->isDefaultSelection($filterValue)) {
            return true;
        }

        $normalizedFilter = strtolower(trim($filterValue));
        
        // Se o filtro for numérico, compara com ID numérico primeiro
        if (is_numeric($filterValue) && $candidateIdNum !== null) {
            $normalizedIdNum = strtolower(trim($candidateIdNum));
            if ($normalizedIdNum === $normalizedFilter) {
                return true;
            }
        }
        
        $candidates = array_filter([$candidate1, $candidate2, $candidateIdNum]);
        foreach ($candidates as $candidate) {
            if (!$candidate) {
                continue;
            }
            $normalizedCandidate = strtolower(trim($candidate));
            if ($normalizedCandidate === $normalizedFilter) {
                return true;
            }
            // Comparação simplificada (remove acentos e espaços)
            $simplifiedFilter = $this->simplifyText($filterValue);
            $simplifiedCandidate = $this->simplifyText($candidate);
            if ($simplifiedCandidate === $simplifiedFilter) {
                return true;
            }
        }

        return false;
    }

    /**
     * Simplifica texto removendo acentos e espaços
     */
    private function simplifyText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove acentos usando transliteração
        if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        } else {
            // Fallback: remove caracteres acentuados manualmente
            $text = str_replace(
                ['á', 'à', 'ã', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'õ', 'ô', 'ö', 'ú', 'ù', 'û', 'ü', 'ç', 'ñ'],
                ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c', 'n'],
                $text
            );
        }
        
        $text = preg_replace('/[^a-z0-9]/', '', $text);
        return trim($text);
    }
}

