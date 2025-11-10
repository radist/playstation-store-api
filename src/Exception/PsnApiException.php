<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Exception;

use Exception;

/**
 * Base exception for all PlayStation Store API related errors
 */
abstract class PsnApiException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
        public readonly ?int $httpStatusCode = null,
        public readonly ?array $responseData = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
