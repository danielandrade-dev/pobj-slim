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
     *     @OA\Parameter(
     *         name="dataInicio",
     *         in="query",
     *         description="Data de início (YYYY-MM-DD)",
     *         required=false,
     *         
     *         format="date"
     *     ),
     *     @OA\Parameter(
     *         name="dataFim",
     *         in="query",
     *         description="Data de fim (YYYY-MM-DD)",
     *         required=false,
     *         
     *         format="date"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
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
        $result = $this->produtoUseCase->handle($filters);
        
        return $this->success($result);
    }

    /**
     * Retorna produtos com dados mensais
     * 
     * @Route("/api/produtos/mensais", name="api_produtos_mensais", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/produtos/mensais",
     *     summary="Produtos mensais",
     *     description="Retorna produtos com dados agrupados por mês",
     *     tags={"POBJ", "Produtos"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="dataInicio",
     *         in="query",
     *         description="Data de início (YYYY-MM-DD)",
     *         required=false,
     *         
     *         format="date"
     *     ),
     *     @OA\Parameter(
     *         name="dataFim",
     *         in="query",
     *         description="Data de fim (YYYY-MM-DD)",
     *         required=false,
     *         
     *         format="date"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produtos mensais retornados com sucesso",
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
    public function handleMonthly(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->produtoUseCase->handleMonthly($filters);
        
        return $this->success($result);
    }
}



