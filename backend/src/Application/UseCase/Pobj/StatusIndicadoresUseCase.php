<?php

namespace App\Application\UseCase\Pobj;

use App\Infrastructure\Persistence\Pobj\StatusIndicadoresRepository;

/**
 * UseCase para operações relacionadas a status de indicadores
 */
class StatusIndicadoresUseCase
{
    private $statusRepository;

    public function __construct(StatusIndicadoresRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * Retorna todos os status de indicadores
     * @return array
     */
    public function handle(): array
    {
        return $this->statusRepository->findAllAsArray();
    }
}

