<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaStatusUseCase;
use App\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller para operações relacionadas a status Omega
 */
class OmegaStatusController extends ControllerBase
{
    private $omegaStatusUseCase;

    public function __construct(OmegaStatusUseCase $omegaStatusUseCase)
    {
        $this->omegaStatusUseCase = $omegaStatusUseCase;
    }

    /**
     * @Route("/api/omega/statuses", name="api_omega_statuses", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->omegaStatusUseCase->getAllStatus();
        
        return $this->success($result);
    }
}

