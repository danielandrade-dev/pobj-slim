<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\CampanhasUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampanhasController extends ControllerBase
{
    private $campanhasUseCase;

    public function __construct(CampanhasUseCase $campanhasUseCase)
    {
        $this->campanhasUseCase = $campanhasUseCase;
    }

    /**
     * @Route("/api/campanhas", name="api_campanhas", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->campanhasUseCase->handle($filters);
        
        return $this->success($result);
    }
}

