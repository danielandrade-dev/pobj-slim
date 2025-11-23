<?php

namespace App\Presentation\Controllers;

use App\Application\UseCase\LeadsUseCase;
use App\Domain\DTO\FilterDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LeadsController extends ControllerBase
{
    private $leadsUseCase;

    public function __construct(LeadsUseCase $leadsUseCase)
    {
        $this->leadsUseCase = $leadsUseCase;
    }

    public function handle(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filters = new FilterDTO($queryParams);
        
        $result = $this->leadsUseCase->handle($filters);
        
        return $this->success($response, $result);
    }
}

