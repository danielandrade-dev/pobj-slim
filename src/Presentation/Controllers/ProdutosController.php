<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\ProdutoUseCase;
use App\Domain\DTO\FilterDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProdutosController extends ControllerBase
{
    private $produtoUseCase;

    public function __construct(ProdutoUseCase $produtoUseCase)
    {
        $this->produtoUseCase = $produtoUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filters = new FilterDTO($queryParams);
        
        $result = $this->produtoUseCase->handle($filters);
        
        return $this->success($response, $result);
    }
}

