<?php

namespace App\Application\UseCase;

use App\Infrastructure\Persistence\CalendarioRepository;

/**
 * UseCase para operações relacionadas ao calendário
 */
class CalendarioUseCase extends AbstractUseCase
{
    public function __construct(CalendarioRepository $repository)
    {
        parent::__construct($repository);
    }
}

