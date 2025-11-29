<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\FHistoricoRankingPobjRepository;

class RankingUseCase
{
    private $repository;

    public function __construct(FHistoricoRankingPobjRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($filters = null): array
    {
        return $this->repository->findAllOrderedByRanking();
    }
}

