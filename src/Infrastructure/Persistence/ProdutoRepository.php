<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\DTO\FilterDTO;
use App\Domain\DTO\ProdutoDTO;
use App\Domain\Entity\DProduto;
use App\Domain\Enum\Tables;
use App\Infrastructure\Helpers\ValueFormatter;

/**
 * Repositório para buscar todos os registros de produtos com filtros opcionais
 */
class ProdutoRepository extends BaseRepository
{
    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, ProdutoDTO::class);
    }

    /**
     * Retorna o SELECT completo da consulta
     * @return string
     */
    public function baseSelect(): string
    {
        return "SELECT 
                    id,
                    id_familia,
                    familia,
                    id_indicador,
                    indicador,
                    id_subindicador,
                    subindicador,
                    metrica,
                    MAX(peso) as peso
                FROM " . Tables::D_PRODUTOS . "
                WHERE 1=1";
    }

    /**
     * Constrói os filtros WHERE baseado no FilterDTO
     * @param FilterDTO|null $filters
     * @return array ['sql' => string, 'params' => array]
     */
    public function builderFilter(FilterDTO $filters = null): array
    {
        $sql = "";
        $params = [];

        if ($filters === null || !$filters->hasAnyFilter()) {
            return ['sql' => $sql, 'params' => $params];
        }

        if ($filters->familia !== null) {
            $sql .= " AND id_familia = :familia";
            $params[':familia'] = $filters->familia;
        }

        if ($filters->indicador !== null) {
            $sql .= " AND id_indicador = :indicador";
            $params[':indicador'] = $filters->indicador;
        }

        if ($filters->subindicador !== null) {
            $sql .= " AND id_subindicador = :subindicador";
            $params[':subindicador'] = $filters->subindicador;
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Retorna a cláusula ORDER BY e GROUP BY
     * @return string
     */
    protected function getOrderBy(): string
    {
        return "GROUP BY 
                    id,
                    id_familia,
                    familia,
                    id_indicador,
                    indicador,
                    id_subindicador,
                    subindicador,
                    metrica
                ORDER BY familia ASC, indicador ASC, subindicador ASC";
    }

    /**
     * Mapeia um array de resultados para ProdutoDTO
     * @param array $row
     * @return ProdutoDTO
     */
    public function mapToDto(array $row): ProdutoDTO
    {
        $entity = DProduto::fromArray($row);
        return $entity->toDTO();
    }
}
