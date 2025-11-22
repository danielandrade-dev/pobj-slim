<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\DTO\CalendarioDTO;
use App\Infrastructure\Helpers\DateFormatter;

class CalendarioRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllAsArray(): array
    {
        $sql = "SELECT 
                    data,
                    ano,
                    mes,
                    mes_nome,
                    dia,
                    dia_da_semana,
                    semana,
                    trimestre,
                    semestre,
                    eh_dia_util
                FROM d_calendario
                ORDER BY data";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function ($row) {
            $dataIso = DateFormatter::toIsoDate(isset($row['data']) ? $row['data'] : null);
            $ehDiaUtil = isset($row['eh_dia_util']) ? ($row['eh_dia_util'] ? 'Sim' : 'Não') : null;
            
            $dto = new CalendarioDTO(
                $dataIso,
                $dataIso,
                isset($row['ano']) ? $row['ano'] : null,
                isset($row['mes']) ? $row['mes'] : null,
                isset($row['mes_nome']) ? $row['mes_nome'] : null,
                isset($row['dia']) ? $row['dia'] : null,
                isset($row['dia_da_semana']) ? $row['dia_da_semana'] : null,
                isset($row['semana']) ? $row['semana'] : null,
                isset($row['trimestre']) ? $row['trimestre'] : null,
                isset($row['semestre']) ? $row['semestre'] : null,
                $ehDiaUtil
            );
            
            return $dto->toArray();
        }, $results);
    }
}

