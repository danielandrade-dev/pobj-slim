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
        return $this->repository->findAllOrderedByData();
    }
}

