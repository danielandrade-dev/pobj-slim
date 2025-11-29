<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\EstruturaUseCase;
use App\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller para operações relacionadas à estrutura organizacional
 */
class EstruturaController extends ControllerBase
{
    private $estruturaUseCase;

    public function __construct(EstruturaUseCase $estruturaUseCase)
    {
        $this->estruturaUseCase = $estruturaUseCase;
    }

    /**
     * @Route("/api/estrutura", name="api_estrutura", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->estruturaUseCase->handle();
        
        return $this->success($result);
    }
}

