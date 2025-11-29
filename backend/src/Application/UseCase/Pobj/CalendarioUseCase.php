<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\CalendarioRepository;

/**
 * UseCase para operações relacionadas ao calendário
 */
class CalendarioUseCase extends AbstractUseCase
{
    public function __construct(CalendarioRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Retorna todos os registros do calendário
     * Calendário não utiliza filtros nem paginação
     * @return array
     */
    public function getAll(): array
    {
        return $this->repository->fetch(null);
    }
}

