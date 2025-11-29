<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\Pobj\HistoricoRepository;

class HistoricoUseCase extends AbstractUseCase
{
    public function __construct(HistoricoRepository $repository)
    {
        parent::__construct($repository);
    }
}

