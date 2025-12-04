<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaDepartamentoRepository;

/**
 * UseCase para operações relacionadas a estrutura Omega
 */
class OmegaStructureUseCase
{
    private $repository;

    public function __construct(OmegaDepartamentoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna toda a estrutura Omega
     * @return array
     */
    public function getStructure(): array
    {
        return $this->repository->findAllOrderedByNome();
    }

    public function handle($filters = null): array
    {
        return $this->getStructure();
    }
}

