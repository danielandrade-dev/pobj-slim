<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\InitUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InitController extends ControllerBase
{
    private $initUseCase;

    public function __construct(InitUseCase $initUseCase)
    {
        $this->initUseCase = $initUseCase;
    }

    /**
     * Retorna dados de inicialização do sistema
     * 
     * @Route("/api/pobj/init", name="api_pobj_init", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/pobj/init",
     *     summary="Dados de inicialização",
     *     description="Retorna estruturas hierárquicas (segmentos, diretorias, regionais, agências, famílias, indicadores, etc.)",
     *     tags={"POBJ", "Inicialização"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados de inicialização retornados com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(
     *                 property="data",
     *                 
     *                 @OA\Property(property="segmentos",  @OA\Items(type="object")),
     *                 @OA\Property(property="diretorias",  @OA\Items(type="object")),
     *                 @OA\Property(property="regionais",  @OA\Items(type="object")),
     *                 @OA\Property(property="agencias",  @OA\Items(type="object")),
     *                 @OA\Property(property="familias",  @OA\Items(type="object")),
     *                 @OA\Property(property="indicadores",  @OA\Items(type="object")),
     *                 @OA\Property(property="subindicadores",  @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=false),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="error",  example="API Key inválida"),
     *                 @OA\Property(property="code",  example="UNAUTHORIZED")
     *             )
     *         )
     *     )
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->initUseCase->handle();
        
        return $this->success($result);
    }
}



