<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\DTO\PontosDTO;
use App\Infrastructure\Helpers\DateFormatter;

class PontosRepository
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
                    funcional,
                    id_indicador,
                    id_familia,
                    indicador,
                    meta,
                    realizado,
                    data_realizado,
                    dt_atualizacao
                FROM f_pontos
                ORDER BY data_realizado DESC, id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function ($row) {
            $dataRealizadoIso = DateFormatter::toIsoDate(isset($row['data_realizado']) ? $row['data_realizado'] : null);
            $dtAtualizacaoIso = isset($row['dt_atualizacao']) ? DateFormatter::toIsoDate($row['dt_atualizacao']) : null;
            
            $dto = new PontosDTO(
                isset($row['id']) ? (int)$row['id'] : null,
                isset($row['funcional']) ? $row['funcional'] : null,
                isset($row['id_indicador']) ? (int)$row['id_indicador'] : null,
                isset($row['id_familia']) ? (int)$row['id_familia'] : null,
                isset($row['indicador']) ? $row['indicador'] : null,
                $this->toFloat(isset($row['meta']) ? $row['meta'] : null),
                $this->toFloat(isset($row['realizado']) ? $row['realizado'] : null),
                $dataRealizadoIso,
                $dtAtualizacaoIso
            );
            
            return $dto->toArray();
        }, $results);
    }
    
    private function toFloat($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        return null;
    }
}

