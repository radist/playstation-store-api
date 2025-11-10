<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

/**
 * Exception thrown when the API returns a 404 Not Found error
 */
final class PsnApiNotFoundException extends PsnApiException
{
    public function __construct(
        string $message = 'Not Found',
        int $code = 404,
        ?\Exception $previous = null,
        ?array $responseData = null,
    ) {
        parent::__construct($message, $code, $previous, 404, $responseData);
    }
}
