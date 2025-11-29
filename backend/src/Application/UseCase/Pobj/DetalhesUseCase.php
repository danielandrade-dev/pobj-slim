<?php

namespace App\Application\UseCase\Pobj;

use App\Domain\DTO\FilterDTO;
use App\Repository\Pobj\FDetalhesRepository;

class DetalhesUseCase
{
    private $repository;

    public function __construct(FDetalhesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(?FilterDTO $filters = null): array
    {
        return $this->repository->findDetalhes($filters);
    }
}

