<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\RealizadosRepository;

class RealizadoUseCase extends AbstractUseCase
{
    public function __construct(RealizadosRepository $repository)
    {
        parent::__construct($repository);
    }
}

