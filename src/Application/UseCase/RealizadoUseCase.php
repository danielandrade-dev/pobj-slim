<?php

namespace App\Application\UseCase;

use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\RealizadosRepository;
use App\Infrastructure\Persistence\Interface\UseCaseInterface;
/**
 * UseCase para operações relacionadas a realizados
 */
class RealizadoUseCase extends AbstractUseCase
{
    public function __construct(RealizadosRepository $repository)
    {
        parent::__construct($repository);
    }

}

