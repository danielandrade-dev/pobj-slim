<?php

namespace App\Application\UseCase\Omega;

use App\Application\UseCase\AbstractUseCase;
use App\Repository\Omega\OmegaMesuRepository;

class OmegaMesuUseCase
{
    private $repository;

    public function __construct(OmegaMesuRepository $repository)
    {
        $this->repository = $repository;
    }
}
