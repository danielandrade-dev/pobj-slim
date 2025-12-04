<?php

namespace App\Application\UseCase\Omega;

use App\Repository\Omega\OmegaUsuarioRepository;


class OmegaUsersUseCase
{
    private $repository;

    public function __construct(OmegaUsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getAllUsers(): array
    {
        return $this->repository->findAllOrderedByNome();
    }

    public function handle($filters = null): array
    {
        return $this->getAllUsers();
    }
}

