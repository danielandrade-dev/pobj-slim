<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\DetalhesUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DetalhesController extends ControllerBase
{
    private $detalhesUseCase;

    public function __construct(DetalhesUseCase $detalhesUseCase)
    {
        $this->detalhesUseCase = $detalhesUseCase;
    }

    /**
     * Retorna detalhes de produtos/indicadores
     * 
     * @Route("/api/pobj/detalhes", name="api_pobj_detalhes", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/pobj/detalhes",
     *     summary="Detalhes de produtos",
     *     description="Retorna detalhamento de produtos e indicadores com filtros",
     *     tags={"POBJ", "Detalhes"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="dataInicio",
     *         in="query",
     *         description="Data de início (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="dataFim",
     *         in="query",
     *         description="Data de fim (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="produtoId",
     *         in="query",
     *         description="ID do produto",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes retornados com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(property="data",  @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=429, description="Rate limit excedido")
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->detalhesUseCase->handle($filters);
        
        return $this->success($result);
    }
}



