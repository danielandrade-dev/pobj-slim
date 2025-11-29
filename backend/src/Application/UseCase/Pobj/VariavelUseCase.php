<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\Pobj\VariavelRepository;

class VariavelUseCase extends AbstractUseCase
{
    public function __construct(VariavelRepository $repository)
    {
        parent::__construct($repository);
    }
}

