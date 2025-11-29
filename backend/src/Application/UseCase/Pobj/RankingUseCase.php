<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\RankingRepository;

class RankingUseCase extends AbstractUseCase
{
    public function __construct(RankingRepository $repository)
    {
        parent::__construct($repository);
    }
}

