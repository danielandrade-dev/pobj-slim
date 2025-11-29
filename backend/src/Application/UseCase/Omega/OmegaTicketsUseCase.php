<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaChamadoRepository;

/**
 * UseCase para operações relacionadas a tickets Omega
 */
class OmegaTicketsUseCase
{
    private $repository;

    public function __construct(OmegaChamadoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todos os tickets Omega
     * @return array
     */
    public function getAllTickets(): array
    {
        return $this->repository->findAllOrderedByUpdated();
    }

    public function handle($filters = null): array
    {
        return $this->getAllTickets();
    }
}

