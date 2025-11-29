<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\HistoricoUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HistoricoController extends ControllerBase
{
    private $historicoUseCase;

    public function __construct(HistoricoUseCase $historicoUseCase)
    {
        $this->historicoUseCase = $historicoUseCase;
    }

    /**
     * @Route("/api/historico", name="api_historico", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->historicoUseCase->handle($filters);
        
        return $this->success($result);
    }
}

