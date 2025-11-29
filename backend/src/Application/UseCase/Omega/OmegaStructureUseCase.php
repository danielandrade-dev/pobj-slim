<?php

namespace App\Application\UseCase\Omega;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Omega\OmegaStructureRepository;

/**
 * UseCase para operações relacionadas a estrutura Omega
 */
class OmegaStructureUseCase extends AbstractUseCase
{
    /**
     * @param OmegaStructureRepository $repository
     */
    public function __construct(OmegaStructureRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Retorna toda a estrutura Omega
     * @return array
     */
    public function getStructure(): array
    {
        return $this->repository->fetch(null);
    }
}

