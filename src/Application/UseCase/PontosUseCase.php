<?php

namespace App\Application\UseCase;

use App\Infrastructure\Persistence\PontosRepository;

class PontosUseCase
{
    private $repository;

    public function __construct(PontosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPontos(): array
    {
        return $this->repository->findAllAsArray();
    }
}

