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
                    metrica,
                    MAX(peso) as peso
                FROM d_produtos
                GROUP BY 
                    id,
                    id_familia,
                    familia,
                    id_indicador,
                    indicador,
                    id_subindicador,
                    subindicador,
                    metrica
                ORDER BY familia ASC, indicador ASC, subindicador ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return ProdutoDTO::fromRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
