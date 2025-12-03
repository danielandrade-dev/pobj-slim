<?php

namespace App\EventSubscriber;

use App\Exception\BadRequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Rate Limiting Subscriber
 * Implementa rate limiting simples baseado em IP e endpoint
 */
class RateLimitSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $rateLimits;
    private $storage = [];

    public function __construct(LoggerInterface $logger, array $rateLimits = [])
    {
        $this->logger = $logger;
        $this->rateLimits = array_merge([
            'default' => ['limit' => 100, 'window' => 60], // 100 requests por minuto
            'auth' => ['limit' => 5, 'window' => 60], // 5 tentativas de auth por minuto
            'api' => ['limit' => 1000, 'window' => 3600], // 1000 requests por hora
        ], $rateLimits);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Ignora requisições OPTIONS (CORS preflight)
        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        // Determina o tipo de rate limit baseado na rota
        $rateLimitType = $this->getRateLimitType($request);
        $config = $this->rateLimits[$rateLimitType] ?? $this->rateLimits['default'];

        // Identifica o cliente
        $clientId = $this->getClientIdentifier($request);
        $key = $rateLimitType . ':' . $clientId;

        // Verifica rate limit
        if (!$this->checkRateLimit($key, $config['limit'], $config['window'])) {
            $this->logger->warning('Rate limit excedido', [
                'client_id' => $clientId,
                'rate_limit_type' => $rateLimitType,
                'limit' => $config['limit'],
                'window' => $config['window'],
                'path' => $request->getPathInfo(),
            ]);

            $response = new JsonResponse([
                'success' => false,
                'data' => [
                    'error' => 'Muitas requisições. Tente novamente mais tarde.',
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'details' => [
                        'limit' => $config['limit'],
                        'window' => $config['window'],
                        'retry_after' => $this->getRetryAfter($key, $config['window']),
                    ],
                    'timestamp' => date('c'),
                ]
            ], 429);

            $response->headers->set('Retry-After', $this->getRetryAfter($key, $config['window']));
            $response->headers->set('X-RateLimit-Limit', $config['limit']);
            $response->headers->set('X-RateLimit-Remaining', $this->getRemaining($key, $config['limit']));

            $event->setResponse($response);
        } else {
            // Adiciona headers de rate limit
            $response = $event->getResponse();
            if ($response) {
                $response->headers->set('X-RateLimit-Limit', $config['limit']);
                $response->headers->set('X-RateLimit-Remaining', $this->getRemaining($key, $config['limit']));
            }
        }
    }

    private function getRateLimitType(Request $request): string
    {
        $path = $request->getPathInfo();

        if (strpos($path, '/api/auth') === 0 || strpos($path, '/login') === 0) {
            return 'auth';
        }

        if (strpos($path, '/api/') === 0) {
            return 'api';
        }

        return 'default';
    }

    private function getClientIdentifier(Request $request): string
    {
        // Rate limiting baseado apenas no IP
        $ip = $request->getClientIp();
        if ($ip) {
            return 'ip:' . $ip;
        }

        // Fallback para session ID (caso não tenha IP)
        return 'session:' . ($request->getSession() ? $request->getSession()->getId() : 'unknown');
    }

    private function checkRateLimit(string $key, int $limit, int $window): bool
    {
        $now = time();
        $windowStart = $now - $window;

        // Limpa entradas antigas
        if (isset($this->storage[$key])) {
            $this->storage[$key] = array_filter(
                $this->storage[$key],
                function ($timestamp) use ($windowStart) {
                    return $timestamp > $windowStart;
                }
            );
        } else {
            $this->storage[$key] = [];
        }

        // Verifica se excedeu o limite
        if (count($this->storage[$key]) >= $limit) {
            return false;
        }

        // Adiciona requisição atual
        $this->storage[$key][] = $now;

        return true;
    }

    private function getRemaining(string $key, int $limit): int
    {
        if (!isset($this->storage[$key])) {
            return $limit;
        }

        return max(0, $limit - count($this->storage[$key]));
    }

    private function getRetryAfter(string $key, int $window): int
    {
        if (!isset($this->storage[$key]) || empty($this->storage[$key])) {
            return 0;
        }

        $oldestRequest = min($this->storage[$key]);
        $retryAfter = ($oldestRequest + $window) - time();

        return max(0, $retryAfter);
    }
}

