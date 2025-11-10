<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

/**
 * Exception thrown when the API returns a 5xx Server Error
 */
final class PsnApiServerException extends PsnApiException
{
    public function __construct(
        string $message = 'Internal Server Error',
        int $code = 500,
        ?\Exception $previous = null,
        ?int $httpStatusCode = null,
        ?array $responseData = null,
    ) {
        $httpStatusCode = $httpStatusCode ?? 500;
        parent::__construct($message, $code, $previous, $httpStatusCode, $responseData);
    }
}
