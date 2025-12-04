<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaDepartamentoRepository;


class OmegaStructureUseCase
{
    private $repository;

    public function __construct(OmegaDepartamentoRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getStructure(): array
    {
        return $this->repository->findAllOrderedByNome();
    }

    public function handle($filters = null): array
    {
        return $this->getStructure();
    }
}

