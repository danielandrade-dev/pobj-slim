<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\StatusIndicadoresUseCase;
use App\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller para operações relacionadas a status de indicadores
 */
class StatusIndicadoresController extends ControllerBase
{
    private $statusIndicadoresUseCase;

    public function __construct(StatusIndicadoresUseCase $statusIndicadoresUseCase)
    {
        $this->statusIndicadoresUseCase = $statusIndicadoresUseCase;
    }

    /**
     * @Route("/api/status_indicadores", name="api_status_indicadores", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->statusIndicadoresUseCase->handle();
        
        return $this->success($result);
    }
}

