<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\RealizadoUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RealizadosController extends ControllerBase
{
    private $realizadoUseCase;

    public function __construct(RealizadoUseCase $realizadoUseCase)
    {
        $this->realizadoUseCase = $realizadoUseCase;
    }

    /**
     * @Route("/api/realizados", name="api_realizados", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->realizadoUseCase->handle($filters);

        return $this->success($result);
    }
}

