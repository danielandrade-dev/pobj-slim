<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\MetaUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MetasController extends ControllerBase
{
    private $metaUseCase;

    public function __construct(MetaUseCase $metaUseCase)
    {
        $this->metaUseCase = $metaUseCase;
    }

    /**
     * @Route("/api/metas", name="api_metas", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        
        $result = $this->metaUseCase->handle($filters);
        
        return $this->success($result);
    }
}

