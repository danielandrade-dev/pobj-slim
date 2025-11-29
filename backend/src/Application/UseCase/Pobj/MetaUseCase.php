<?php

namespace App\Application\UseCase\Pobj;

use App\Application\UseCase\AbstractUseCase;
use App\Infrastructure\Persistence\Pobj\MetasRepository;

/**
 * UseCase para operações relacionadas a metas
 */
class MetaUseCase extends AbstractUseCase
{
    public function __construct(MetasRepository $repository)
    {
        parent::__construct($repository);
    }
}

