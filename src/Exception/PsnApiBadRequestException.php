<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

/**
 * Exception thrown when the API returns a 400 Bad Request error
 */
final class PsnApiBadRequestException extends PsnApiException
{
    public function __construct(
        string $message = 'Bad Request',
        int $code = 400,
        ?\Exception $previous = null,
        ?array $responseData = null,
    ) {
        parent::__construct($message, $code, $previous, 400, $responseData);
    }
}
