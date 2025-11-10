<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

/**
 * Factory for creating HTTP exceptions based on status codes
 */
final class HttpExceptionFactory
{
    /**
     * Create an appropriate exception based on HTTP status code
     *
     * @param int $statusCode HTTP status code
     * @param string $message Error message
     * @param array|null $responseData Response data from API
     * @return PsnApiException
     */
    public static function create(int $statusCode, string $message = '', ?array $responseData = null): PsnApiException
    {
        return match ($statusCode) {
            400 => new PsnApiBadRequestException($message, $statusCode, null, $responseData),
            403 => new PsnApiForbiddenException($message, $statusCode, null, $responseData),
            404 => new PsnApiNotFoundException($message, $statusCode, null, $responseData),
            500, 502, 503, 504 => new PsnApiServerException($message, $statusCode, null, $statusCode, $responseData),
            default => new PsnApiServerException($message, $statusCode, null, $statusCode, $responseData),
        };
    }

    /**
     * Create an exception for client errors (4xx)
     *
     * @param int $statusCode HTTP status code (4xx)
     * @param string $message Error message
     * @param array|null $responseData Response data from API
     * @return PsnApiException
     */
    public static function createClientError(int $statusCode, string $message = '', ?array $responseData = null): PsnApiException
    {
        if ($statusCode < 400 || $statusCode >= 500) {
            throw new \InvalidArgumentException('Status code must be in 4xx range for client errors');
        }

        return self::create($statusCode, $message, $responseData);
    }

    /**
     * Create an exception for server errors (5xx)
     *
     * @param int $statusCode HTTP status code (5xx)
     * @param string $message Error message
     * @param array|null $responseData Response data from API
     * @return PsnApiException
     */
    public static function createServerError(int $statusCode, string $message = '', ?array $responseData = null): PsnApiException
    {
        if ($statusCode < 500 || $statusCode >= 600) {
            throw new \InvalidArgumentException('Status code must be in 5xx range for server errors');
        }

        return self::create($statusCode, $message, $responseData);
    }
}
