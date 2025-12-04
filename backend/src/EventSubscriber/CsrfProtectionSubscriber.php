<?php

namespace App\EventSubscriber;

use App\Exception\BadRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * CSRF Protection Subscriber
 * Valida tokens CSRF para requisições POST, PUT, DELETE, PATCH
 */
class CsrfProtectionSubscriber implements EventSubscriberInterface
{
    private $csrfTokenManager;
    private $enabled;
    private $excludedPaths;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        bool $enabled = true,
        array $excludedPaths = []
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->enabled = $enabled;
        $this->excludedPaths = array_merge([
            '/api/auth/login',
            '/api/auth/register',
        ], $excludedPaths);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 5],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $request = $event->getRequest();

        // Aplica apenas para métodos que modificam dados
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return;
        }

        // Ignora paths excluídos
        if ($this->isPathExcluded($request->getPathInfo())) {
            return;
        }

        // Ignora requisições OPTIONS (CORS preflight)
        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        // Verifica se há token CSRF
        $token = $request->headers->get('X-CSRF-Token') 
              ?? $request->request->get('_token')
              ?? $request->query->get('_token');

        if (!$token) {
            throw new BadRequestException('Token CSRF não fornecido', [
                'csrf_required' => true,
            ]);
        }

        // Valida o token
        $tokenId = $request->getPathInfo();
        $csrfToken = new CsrfToken($tokenId, $token);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new BadRequestException('Token CSRF inválido', [
                'csrf_invalid' => true,
            ]);
        }
    }

    private function isPathExcluded(string $path): bool
    {
        foreach ($this->excludedPaths as $excludedPath) {
            if (strpos($path, $excludedPath) === 0) {
                return true;
            }
        }

        return false;
    }
}

