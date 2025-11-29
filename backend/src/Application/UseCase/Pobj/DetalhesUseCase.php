<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FDetalhesRepository;

class DetalhesUseCase
{
    private $repository;

    public function __construct(FDetalhesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByData();
    }
}

