<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FRealizadosRepository;

class RealizadoUseCase
{
    private $repository;

    public function __construct(FRealizadosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByData();
    }
}

