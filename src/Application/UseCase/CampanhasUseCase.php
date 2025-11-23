<?php

namespace App\Application\UseCase;

use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\CampanhasRepository;

class CampanhasUseCase extends AbstractUseCase
{
    public function __construct(CampanhasRepository $repository)
    {
        parent::__construct($repository);
    }
}

