<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\Pobj\DetalhesRepository;

class DetalhesUseCase extends AbstractUseCase
{
    public function __construct(DetalhesRepository $repository)
    {
        parent::__construct($repository);
    }
}

