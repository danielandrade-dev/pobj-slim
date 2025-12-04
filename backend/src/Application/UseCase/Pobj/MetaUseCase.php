<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FMetaRepository;


class MetaUseCase
{
    private $repository;

    public function __construct(FMetaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByData();
    }
}

