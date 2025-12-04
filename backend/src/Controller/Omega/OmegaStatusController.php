<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaStatusUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OmegaStatusController extends ControllerBase
{
    private $omegaStatusUseCase;

    public function __construct(OmegaStatusUseCase $omegaStatusUseCase)
    {
        $this->omegaStatusUseCase = $omegaStatusUseCase;
    }

    /**
     * Retorna lista de status disponíveis
     * 
     * @Route("/api/omega/statuses", name="api_omega_statuses", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/omega/statuses",
     *     summary="Lista de status",
     *     description="Retorna todos os status disponíveis para tickets do sistema Omega",
     *     tags={"Omega", "Status"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de status retornada com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(
     *                 property="data",
     *                 
     *                 @OA\Items(
     *                     
     *                     @OA\Property(property="id",  example="aberto"),
     *                     @OA\Property(property="name",  example="Aberto"),
     *                     @OA\Property(property="description",  example="Ticket aberto e aguardando atendimento")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=429, description="Rate limit excedido")
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->omegaStatusUseCase->getAllStatus();
        
        return $this->success($result);
    }
}



