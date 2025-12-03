<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaStructureUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OmegaStructureController extends ControllerBase
{
    private $omegaStructureUseCase;

    public function __construct(OmegaStructureUseCase $omegaStructureUseCase)
    {
        $this->omegaStructureUseCase = $omegaStructureUseCase;
    }

    /**
     * Retorna estrutura organizacional do Omega
     * 
     * @Route("/api/omega/structure", name="api_omega_structure", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/omega/structure",
     *     summary="Estrutura organizacional",
     *     description="Retorna a estrutura organizacional do sistema Omega (hierarquia de times, supervisores, etc.)",
     *     tags={"Omega", "Estrutura"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estrutura retornada com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="teams",  @OA\Items(type="object")),
     *                 @OA\Property(property="hierarchy", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=429, description="Rate limit excedido")
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->omegaStructureUseCase->getStructure();
        
        return $this->success($result);
    }
}



