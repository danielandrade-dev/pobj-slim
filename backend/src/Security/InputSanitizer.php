<?php

namespace App\Security;

/**
 * Classe para sanitização de inputs
 * Previne SQL Injection, XSS e outros ataques
 */
class InputSanitizer
{
    /**
     * Sanitiza uma string removendo caracteres perigosos
     */
    public static function sanitizeString(?string $input, bool $allowHtml = false): ?string
    {
        if ($input === null) {
            return null;
        }

        // Remove null bytes
        $input = str_replace("\0", '', $input);

        // Remove caracteres de controle
        $input = preg_replace('/[\x00-\x1F\x7F]/', '', $input);

        if (!$allowHtml) {
            // Remove tags HTML e PHP
            $input = strip_tags($input);
            // Escapa caracteres especiais HTML
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return trim($input);
    }

    /**
     * Sanitiza um array recursivamente
     */
    public static function sanitizeArray(array $input, bool $allowHtml = false): array
    {
        $sanitized = [];

        foreach ($input as $key => $value) {
            $sanitizedKey = self::sanitizeString($key, $allowHtml);

            if (is_array($value)) {
                $sanitized[$sanitizedKey] = self::sanitizeArray($value, $allowHtml);
            } elseif (is_string($value)) {
                $sanitized[$sanitizedKey] = self::sanitizeString($value, $allowHtml);
            } else {
                $sanitized[$sanitizedKey] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Valida e sanitiza um email
     */
    public static function sanitizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        return $email ?: null;
    }

    /**
     * Sanitiza um número inteiro
     */
    public static function sanitizeInt($input, int $min = null, int $max = null): ?int
    {
        if ($input === null) {
            return null;
        }

        $value = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        $value = filter_var($value, FILTER_VALIDATE_INT);

        if ($value === false) {
            return null;
        }

        if ($min !== null && $value < $min) {
            return null;
        }

        if ($max !== null && $value > $max) {
            return null;
        }

        return $value;
    }

    /**
     * Sanitiza um número float
     */
    public static function sanitizeFloat($input, float $min = null, float $max = null): ?float
    {
        if ($input === null) {
            return null;
        }

        $value = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);

        if ($value === false) {
            return null;
        }

        if ($min !== null && $value < $min) {
            return null;
        }

        if ($max !== null && $value > $max) {
            return null;
        }

        return $value;
    }

    /**
     * Sanitiza uma URL
     */
    public static function sanitizeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = filter_var($url, FILTER_VALIDATE_URL);

        return $url ?: null;
    }

    /**
     * Remove SQL injection patterns
     */
    public static function preventSqlInjection(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Remove padrões comuns de SQL injection
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\bSCRIPT\b)/i',
            '/(--|#|\/\*|\*\/)/',
            '/(\bOR\b.*=.*)/i',
            '/(\bAND\b.*=.*)/i',
        ];

        $input = preg_replace($patterns, '', $input);

        return $input;
    }

    /**
     * Sanitiza dados de entrada de uma requisição
     */
    public static function sanitizeRequestData(array $data, bool $allowHtml = false): array
    {
        $sanitized = self::sanitizeArray($data, $allowHtml);

        // Aplica prevenção adicional de SQL injection em strings
        array_walk_recursive($sanitized, function (&$value) {
            if (is_string($value)) {
                $value = self::preventSqlInjection($value);
            }
        });

        return $sanitized;
    }
}

