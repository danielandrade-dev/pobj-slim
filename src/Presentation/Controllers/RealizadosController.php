<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\RealizadoUseCase;
use App\Domain\DTO\FilterDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RealizadosController extends ControllerBase
{
    private $realizadoUseCase;

    public function __construct(RealizadoUseCase $realizadoUseCase)
    {
        $this->realizadoUseCase = $realizadoUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filters = new FilterDTO($queryParams);

        $result = $this->realizadoUseCase->handle($filters);

        return $this->success($response, $result);
    }
}
