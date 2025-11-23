<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\DetalhesUseCase;
use App\Domain\DTO\FilterDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DetalhesController extends ControllerBase
{
    private $detalhesUseCase;

    public function __construct(DetalhesUseCase $detalhesUseCase)
    {
        $this->detalhesUseCase = $detalhesUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filters = new FilterDTO($queryParams);
        
        $result = $this->detalhesUseCase->handle($filters);
        
        return $this->success($response, $result);
    }
}

