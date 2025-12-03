<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaMesuUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OmegaMesuController extends ControllerBase
{
    private $omegaMesuUseCase;

    public function __construct(OmegaMesuUseCase $omegaMesuUseCase)
    {
        $this->omegaMesuUseCase = $omegaMesuUseCase;
    }

    /**
     * Retorna dados do MESU (Métricas e Estatísticas de Uso)
     * 
     * @Route("/api/omega/mesu", name="api_omega_mesu", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/omega/mesu",
     *     summary="Métricas e Estatísticas de Uso",
     *     description="Retorna métricas e estatísticas de uso do sistema Omega",
     *     tags={"Omega", "Métricas"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="dataInicio",
     *         in="query",
     *         description="Data de início (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema( format="date")
     *     ),
     *     @OA\Parameter(
     *         name="dataFim",
     *         in="query",
     *         description="Data de fim (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema( format="date")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         description="ID do usuário para filtrar métricas",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Métricas retornadas com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="metrics",  @OA\Items(type="object")),
     *                 @OA\Property(property="statistics", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=429, description="Rate limit excedido")
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->omegaMesuUseCase->handle($filters);
            
        return $this->success($result);
    }
}




