<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\DTO\ProdutoDTO;
use App\Infrastructure\Helpers\RowMapper;

class ProdutoRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllAsArray(): array
    {
        $sql = "SELECT 
                    id,
                    id_familia,
                    familia,
                    id_indicador,
                    indicador,
                    id_subindicador,
                    subindicador,
                    peso
                FROM d_produtos
                ORDER BY familia ASC, indicador ASC, subindicador ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function ($row) {
            $dto = new ProdutoDTO(
                isset($row['id']) ? $row['id'] : null,
                RowMapper::toString(isset($row['id_familia']) ? $row['id_familia'] : null),
                isset($row['familia']) ? $row['familia'] : '',
                RowMapper::toString(isset($row['id_indicador']) ? $row['id_indicador'] : null),
                isset($row['indicador']) ? $row['indicador'] : '',
                RowMapper::toString(isset($row['id_subindicador']) ? $row['id_subindicador'] : null),
                isset($row['subindicador']) ? $row['subindicador'] : '',
                RowMapper::toFloat(isset($row['peso']) ? $row['peso'] : null)
            );
            
            return $dto->toArray();
        }, $results);
    }
}
