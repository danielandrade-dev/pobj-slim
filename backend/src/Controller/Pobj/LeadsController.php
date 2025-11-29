<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\LeadsUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LeadsController extends ControllerBase
{
    private $leadsUseCase;

    public function __construct(LeadsUseCase $leadsUseCase)
    {
        $this->leadsUseCase = $leadsUseCase;
    }

    /**
     * @Route("/api/leads", name="api_leads", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->leadsUseCase->handle($filters);
        
        return $this->success($result);
    }
}

