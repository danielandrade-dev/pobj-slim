<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\ProdutoUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProdutosController extends ControllerBase
{
    private $produtoUseCase;

    public function __construct(ProdutoUseCase $produtoUseCase)
    {
        $this->produtoUseCase = $produtoUseCase;
    }
    /**
     * Retorna lista de produtos
     * 
     * @Route("/api/produtos", name="api_produtos", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/produtos",
     *     summary="Lista de produtos",
     *     description="Retorna lista de produtos com filtros opcionais",
     *     tags={"POBJ", "Produtos"},
     *     security={{"ApiKeyAuth": {}}},
     *
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
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
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
        $result = $this->produtoUseCase->handle($filters);
        
        return $this->success($result);
    }

}



