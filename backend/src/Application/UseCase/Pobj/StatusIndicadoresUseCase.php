<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\DStatusIndicadorRepository;


class StatusIndicadoresUseCase
{
    private $statusRepository;

    public function __construct(DStatusIndicadorRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    
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

