<?php

namespace App\Presentation\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

abstract class ControllerBase
{
    protected function json(Response $response, $data, int $status = 200): Response
    {
        $response = $response->withStatus($status)
                             ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));

        return $response;
    }

    protected function success(Response $response, $data): Response
    {
        return $this->json($response, [
            'success' => true,
            'data' => $data
        ], 200);
    }

    protected function error(Response $response, string $message, int $status = 400): Response
    {
        return $this->json($response, [
            'success' => false,
            'error' => $message
        ], $status);
    }
}
