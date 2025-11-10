<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

/**
 * Exception thrown when the API returns a 403 Forbidden error
 */
final class PsnApiForbiddenException extends PsnApiException
{
    public function __construct(
        string $message = 'Forbidden',
        int $code = 403,
        ?\Exception $previous = null,
        ?array $responseData = null,
    ) {
        parent::__construct($message, $code, $previous, 403, $responseData);
    }
}
