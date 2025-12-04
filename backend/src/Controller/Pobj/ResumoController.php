<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\ResumoUseCase;
use App\Controller\ControllerBase;
use App\Domain\DTO\FilterDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResumoController extends ControllerBase
{
    private $resumoUseCase;

    public function __construct(ResumoUseCase $resumoUseCase)
    {
        $this->resumoUseCase = $resumoUseCase;
    }

    /**
     * Retorna resumo completo de produtos com dados agregados
     * 
     * @Route("/api/pobj/resumo", name="api_pobj_resumo", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/pobj/resumo",
     *     summary="Retorna resumo de produtos",
     *     description="Retorna resumo completo com produtos, produtos mensais, variáveis e snapshot de negócio",
     *     tags={"POBJ", "Resumo"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="dataInicio",
     *         in="query",
     *         description="Data de início do período (formato: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema( format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="dataFim",
     *         in="query",
     *         description="Data de fim do período (formato: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema( format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="segmentoId",
     *         in="query",
     *         description="ID do segmento",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="diretoriaId",
     *         in="query",
     *         description="ID da diretoria",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="regionalId",
     *         in="query",
     *         description="ID da regional",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="agenciaId",
     *         in="query",
     *         description="ID da agência",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="gerente",
     *         in="query",
     *         description="Funcional do gerente",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="familiaId",
     *         in="query",
     *         description="ID da família",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="indicadorId",
     *         in="query",
     *         description="ID do indicador",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do indicador (01=Atingido, 02=Não Atingido, 03=Todos)",
     *         required=false,
     *         @OA\Schema( enum={"01", "02", "03"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resumo retornado com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(
     *                 property="data",
     *                 
     *                 @OA\Property(property="cards",  @OA\Items(type="object")),
     *                 @OA\Property(property="classifiedCards",  @OA\Items(type="object")),
     *                 @OA\Property(property="variableCard",  @OA\Items(type="object")),
     *                 @OA\Property(property="businessSnapshot", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado - API Key inválida ou ausente",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=false),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="error",  example="API Key inválida"),
     *                 @OA\Property(property="code",  example="UNAUTHORIZED")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit excedido",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=false),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="error",  example="Muitas requisições"),
     *                 @OA\Property(property="code",  example="RATE_LIMIT_EXCEEDED")
     *             )
     *         )
     *     )
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $filters = new FilterDTO($request->query->all());
        $result = $this->resumoUseCase->handle($filters);

        return $this->success($result);
    }
}




