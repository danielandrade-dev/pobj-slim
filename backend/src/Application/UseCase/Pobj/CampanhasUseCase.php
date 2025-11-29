<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\CampanhasRepository;

class CampanhasUseCase extends AbstractUseCase
{
    public function __construct(CampanhasRepository $repository)
    {
        parent::__construct($repository);
    }
}

