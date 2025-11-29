<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaUsuarioRepository;

/**
 * UseCase para operações relacionadas a usuários Omega
 */
class OmegaUsersUseCase
{
    private $repository;

    public function __construct(OmegaUsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todos os usuários Omega
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->repository->findAllOrderedByNome();
    }

    public function handle($filters = null): array
    {
        return $this->getAllUsers();
    }
}

