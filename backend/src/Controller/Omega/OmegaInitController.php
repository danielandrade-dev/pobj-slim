<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaInitUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OmegaInitController extends ControllerBase
{
    private $initUseCase;

    public function __construct(OmegaInitUseCase $initUseCase)
    {
        $this->initUseCase = $initUseCase;
    }

    /**
     * Retorna dados de inicialização do sistema Omega
     * 
     * @Route("/api/omega/init", name="api_omega_init", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/omega/init",
     *     summary="Dados de inicialização Omega",
     *     description="Retorna estruturas hierárquicas e dados iniciais do sistema Omega",
     *     tags={"Omega", "Inicialização"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados de inicialização retornados com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(property="data", 
     *                 @OA\Property(property="users",  @OA\Items(type="object")),
     *                 @OA\Property(property="teams",  @OA\Items(type="object")),
     *                 @OA\Property(property="statuses",  @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=429, description="Rate limit excedido")
     * )
     */
    public function handle(Request $request): JsonResponse
    {
        $result = $this->initUseCase->handle();
        
        return $this->success($result);
    }
}





