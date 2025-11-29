<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\PontosUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PontosController extends ControllerBase
{
    private $pontosUseCase;

    public function __construct(PontosUseCase $pontosUseCase)
    {
        $this->pontosUseCase = $pontosUseCase;
    }

    /**
     * @Route("/api/pontos", name="api_pontos", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->pontosUseCase->handle($filters);
        
        return $this->success($result);
    }
}

