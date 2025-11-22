<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\DTO\ProdutoDTO;

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
            $idFamilia = isset($row['id_familia']) ? $row['id_familia'] : null;
            $idIndicador = isset($row['id_indicador']) ? $row['id_indicador'] : null;
            $idSubindicador = isset($row['id_subindicador']) ? $row['id_subindicador'] : null;
            $peso = isset($row['peso']) ? $row['peso'] : null;
            
            $dto = new ProdutoDTO(
                isset($row['id']) ? $row['id'] : null,
                $idFamilia !== null ? (string)$idFamilia : null,
                isset($row['familia']) ? $row['familia'] : '',
                $idIndicador !== null ? (string)$idIndicador : null,
                isset($row['indicador']) ? $row['indicador'] : '',
                $idSubindicador !== null ? (string)$idSubindicador : null,
                isset($row['subindicador']) ? $row['subindicador'] : '',
                ($peso !== null && $peso !== '' && is_numeric($peso)) ? (float)$peso : null
            );
            
            return $dto->toArray();
        }, $results);
    }
}
