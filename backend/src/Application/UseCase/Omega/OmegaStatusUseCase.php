<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaStatusRepository;

/**
 * UseCase para operações relacionadas a status Omega
 */
class OmegaStatusUseCase
{
    private $repository;

    public function __construct(OmegaStatusRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todos os status Omega
     * @return array
     */
    public function getAllStatus(): array
    {
        return $this->repository->findAllOrdered();
    }

    public function handle($filters = null): array
    {
        return $this->getAllStatus();
    }
}

