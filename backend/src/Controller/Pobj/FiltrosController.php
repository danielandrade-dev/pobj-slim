<?php

namespace App\Controller\Pobj;

use App\Application\UseCase\Pobj\FiltrosUseCase;
use App\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FiltrosController extends ControllerBase
{
    private $filtrosUseCase;

    public function __construct(FiltrosUseCase $filtrosUseCase)
    {
        $this->filtrosUseCase = $filtrosUseCase;
    }

    /**
     * @Route("/api/filtros", name="api_filtros", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $nivelStr = $request->query->get('nivel', '');
        
        if (empty($nivelStr)) {
            return $this->error('Parâmetro "nivel" é obrigatório', 400);
        }

        try {
            $result = $this->filtrosUseCase->getFiltroByNivel($nivelStr);
            
            return new JsonResponse($result, 200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}

