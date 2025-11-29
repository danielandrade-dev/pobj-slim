<?php

namespace App\Application\UseCase\Omega;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Omega\OmegaMesuRepository;

class OmegaMesuUseCase extends AbstractUseCase
{
    public function __construct(OmegaMesuRepository $repository)
    {
        parent::__construct($repository);
    }
}

