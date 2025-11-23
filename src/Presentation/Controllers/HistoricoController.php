<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\HistoricoUseCase;
use App\Domain\DTO\FilterDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HistoricoController extends ControllerBase
{
    private $historicoUseCase;

    public function __construct(HistoricoUseCase $historicoUseCase)
    {
        $this->historicoUseCase = $historicoUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filters = new FilterDTO($queryParams);
        
        $result = $this->historicoUseCase->handle($filters);
        
        return $this->success($response, $result);
    }
}

