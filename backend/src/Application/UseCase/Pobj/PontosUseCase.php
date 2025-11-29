<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\PontosRepository;

class PontosUseCase extends AbstractUseCase
{
    public function __construct(PontosRepository $repository)
    {
        parent::__construct($repository);
    }
}

