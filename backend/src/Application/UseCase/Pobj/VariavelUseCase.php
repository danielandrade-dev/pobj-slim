<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FVariavelRepository;

class VariavelUseCase
{
    private $repository;

    public function __construct(FVariavelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByDataAtualizacao();
    }
}

