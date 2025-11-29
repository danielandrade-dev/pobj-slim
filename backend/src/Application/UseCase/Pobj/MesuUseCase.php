<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Omega\OmegaMesuRepository;

class MesuUseCase extends AbstractUseCase
{
    public function __construct(OmegaMesuRepository $repository)
    {
        parent::__construct($repository);
    }
}

