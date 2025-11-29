<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\Pobj\LeadsRepository;

class LeadsUseCase extends AbstractUseCase
{
    public function __construct(LeadsRepository $repository)
    {
        parent::__construct($repository);
    }
}

