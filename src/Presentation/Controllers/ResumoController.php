<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\ResumoUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ResumoController extends ControllerBase
{
    private ResumoUseCase $resumoUseCase;

    public function __construct(ResumoUseCase $resumoUseCase)
    {
        $this->resumoUseCase = $resumoUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $filters = $request->getAttribute('filters');
        $result = $this->resumoUseCase->handle($filters);

        return $this->success($response, $result);
    }
}

