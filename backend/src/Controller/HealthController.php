<?php
namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController extends AbstractController
{
    /**
     * Health check do sistema
     * 
     * @Route("/api/health", name="api_health", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/health",
     *     summary="Health Check",
     *     description="Verifica o status da API. Endpoint público, não requer autenticação.",
     *     tags={"Sistema"},
     *     @OA\Response(
     *         response=200,
     *         description="API está funcionando",
     *         @OA\JsonContent(
     *             
     *             @OA\Property(property="status",  example="ok"),
     *             @OA\Property(property="timestamp",  example=1701638400)
     *         )
     *     )
     * )
     */
    public function check(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'timestamp' => time()
        ]);
    }
}


