<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FPontosRepository;

class PontosUseCase
{
    private $repository;

    public function __construct(FPontosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByData();
    }
}

