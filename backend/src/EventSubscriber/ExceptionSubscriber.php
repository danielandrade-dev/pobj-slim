<?php

namespace App\EventSubscriber;

use App\Exception\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class ExceptionSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $environment;

    public function __construct(LoggerInterface $logger, string $environment)
    {
        $this->logger = $logger;
        $this->environment = $environment;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

                if ($request->getMethod() === 'OPTIONS') {
            return;
        }

                $this->logException($exception, $request);

                $response = $this->createResponse($exception);

        $event->setResponse($response);
    }

    private function createResponse(\Throwable $exception): JsonResponse
    {
        $isDev = $this->environment === 'dev';

                if ($exception instanceof AppException) {
            $statusCode = $exception->getStatusCode();
            $errorData = $exception->toArray();
        } 
                elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $errorData = [
                'error' => $exception->getMessage() ?: 'Erro HTTP',
                'code' => 'HTTP_ERROR',
                'details' => [],
            ];
        }
                elseif ($exception instanceof \Symfony\Component\Validator\Exception\ValidationFailedException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $violations = [];
            foreach ($exception->getViolations() as $violation) {
                $violations[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
            $errorData = [
                'error' => 'Erro de validação',
                'code' => 'VALIDATION_ERROR',
                'details' => ['validation_errors' => $violations],
            ];
        }
                else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $errorData = [
                'error' => $isDev ? $exception->getMessage() : 'Erro interno do servidor',
                'code' => 'INTERNAL_ERROR',
                'details' => [],
            ];
        }

                if ($isDev) {
            $errorData['debug'] = [
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->formatStackTrace($exception),
            ];
        }

                $errorData['timestamp'] = date('c');
        if (isset($_SERVER['REQUEST_ID'])) {
            $errorData['request_id'] = $_SERVER['REQUEST_ID'];
        }

        $responseData = [
            'success' => false,
            'data' => $errorData,
        ];

        $response = new JsonResponse(
            $responseData,
            $statusCode,
            ['Content-Type' => 'application/json; charset=utf-8']
        );

                $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $response;
    }

    private function logException(\Throwable $exception, $request): void
    {
        $context = [
            'exception' => $exception,
            'request_uri' => $request->getUri(),
            'request_method' => $request->getMethod(),
            'request_headers' => $request->headers->all(),
            'request_content' => $request->getContent(),
        ];

                if ($exception instanceof AppException) {
            $context['error_code'] = $exception->getErrorCode();
            $context['details'] = $exception->getDetails();
        }

                if ($exception instanceof AppException) {
            $statusCode = $exception->getStatusCode();
        } elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

                if ($statusCode >= 500) {
            $this->logger->error(
                sprintf(
                    'Erro %s: %s em %s:%d',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
                $context
            );
        } elseif ($statusCode >= 400) {
            $this->logger->warning(
                sprintf(
                    'Erro de cliente %s: %s',
                    get_class($exception),
                    $exception->getMessage()
                ),
                $context
            );
        } else {
            $this->logger->info(
                sprintf(
                    'Exceção %s: %s',
                    get_class($exception),
                    $exception->getMessage()
                ),
                $context
            );
        }
    }

    private function formatStackTrace(\Throwable $exception): array
    {
        $trace = [];
        foreach ($exception->getTrace() as $index => $frame) {
            $trace[] = [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null,
            ];
        }
        return $trace;
    }
}

