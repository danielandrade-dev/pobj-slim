<?php

namespace App\Controller\Omega;

use App\Application\UseCase\Omega\OmegaUsersUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OmegaUsersController extends ControllerBase
{
    private $omegaUsersUseCase;

    public function __construct(OmegaUsersUseCase $omegaUsersUseCase)
    {
        $this->omegaUsersUseCase = $omegaUsersUseCase;
    }

    /**
     * Retorna lista de usuários do sistema Omega
     * 
     * @Route("/api/omega/users", name="api_omega_users", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/omega/users",
     *     summary="Lista de usuários Omega",
     *     description="Retorna todos os usuários do sistema Omega (analistas, supervisores, admins)",
     *     tags={"Omega", "Usuários"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(
     *                 property="data",
     *                 
     *                 @OA\Items(
     *                     
     *                     @OA\Property(property="id",  example="user123"),
     *                     @OA\Property(property="name",  example="João Silva"),
     *                     @OA\Property(property="functional",  example="12345"),
     *                     @OA\Property(property="role",  example="analista", enum={"analista", "supervisor", "admin"}),
     *                     @OA\Property(property="analista",  example=true),
     *                     @OA\Property(property="supervisor",  example=false),
     *                     @OA\Property(property="admin",  example=false)
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
        $result = $this->omegaUsersUseCase->getAllUsers();
        
        return $this->success($result);
    }
}



