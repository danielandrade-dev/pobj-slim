<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaStatusRepository;


class OmegaStatusUseCase
{
    private $repository;

    public function __construct(OmegaStatusRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getAllStatus(): array
    {
        return $this->repository->findAllOrdered();
    }

    public function handle($filters = null): array
    {
        return $this->getAllStatus();
    }
}

