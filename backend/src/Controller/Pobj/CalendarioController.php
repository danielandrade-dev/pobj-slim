<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\CalendarioUseCase;
use App\Controller\ControllerBase;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarioController extends ControllerBase
{
    private $calendarioUseCase;

    public function __construct(CalendarioUseCase $calendarioUseCase)
    {
        $this->calendarioUseCase = $calendarioUseCase;
    }

    /**
     * Retorna calendário de eventos/datas importantes
     * 
     * @Route("/api/pobj/calendario", name="api_pobj_calendario", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/pobj/calendario",
     *     summary="Calendário de eventos",
     *     description="Retorna calendário com datas importantes, eventos e marcos do POBJ",
     *     tags={"POBJ", "Calendário"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Calendário retornado com sucesso",
     *         @OA\Schema(
     *             
     *             @OA\Property(property="success",  example=true),
     *             @OA\Property(
     *                 property="data",
     *                 
     *                 @OA\Items(
     *                     
     *                     @OA\Property(property="id",  example=1),
     *                     @OA\Property(property="date",  format="date", example="2024-12-31"),
     *                     @OA\Property(property="title",  example="Fechamento do mês"),
     *                     @OA\Property(property="description",  example="Data limite para fechamento"),
     *                     @OA\Property(property="type",  example="deadline")
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
        $result = $this->calendarioUseCase->getAll();
        
        return $this->success($result);
    }
}



