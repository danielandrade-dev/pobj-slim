<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\DStatusIndicadorRepository;

/**
 * UseCase para operações relacionadas a status de indicadores
 */
class StatusIndicadoresUseCase
{
    private $statusRepository;

    public function __construct(DStatusIndicadorRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * Retorna todos os status de indicadores
     * @return array
     */
    public function handle(): array
    {
        $statuses = $this->statusRepository->findAllOrderedById();
        $result = [];
        foreach ($statuses as $status) {
            $result[] = [
                'id' => $status->getId(),
                'label' => $status->getStatus(),
            ];
        }
        return empty($result) ? [] : $result;
    }
}

