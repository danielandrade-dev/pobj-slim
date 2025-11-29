<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\VariavelUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VariavelController extends ControllerBase
{
    private $variavelUseCase;

    public function __construct(VariavelUseCase $variavelUseCase)
    {
        $this->variavelUseCase = $variavelUseCase;
    }

    /**
     * @Route("/api/variavel", name="api_variavel", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->variavelUseCase->handle($filters);
        
        return $this->success($result);
    }
}

