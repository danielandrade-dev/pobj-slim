<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\DCalendarioRepository;

/**
 * UseCase para operações relacionadas ao calendário
 */
class CalendarioUseCase
{
    private $repository;

    public function __construct(DCalendarioRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todos os registros do calendário
     * Calendário não utiliza filtros nem paginação
     * @return array
     */
    public function getAll(): array
    {
        $calendarios = $this->repository->findAllOrderedByData();
        $result = [];
        
        foreach ($calendarios as $row) {
            $data = $row['data'];
            if ($data instanceof \DateTimeInterface) {
                $data = $data->format('Y-m-d');
            } elseif (is_string($data)) {
                $data = $data;
            } else {
                $data = null;
            }
            
            $result[] = [
                'data' => $data,
                'ano' => $row['ano'] ?? null,
                'mes' => $row['mes'] ?? null,
                'mesNome' => $row['mesNome'] ?? null,
                'dia' => $row['dia'] ?? null,
                'diaDaSemana' => $row['diaDaSemana'] ?? null,
                'semana' => $row['semana'] ?? null,
                'trimestre' => $row['trimestre'] ?? null,
                'semestre' => $row['semestre'] ?? null,
                'ehDiaUtil' => isset($row['ehDiaUtil']) ? ($row['ehDiaUtil'] ? 'Sim' : 'Não') : null,
            ];
        }
        
        return $result;
    }
}

