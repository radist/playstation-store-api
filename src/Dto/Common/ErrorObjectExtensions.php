<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Error object extensions containing additional error details
 */
final readonly class ErrorObjectExtensions
{
    public function __construct(
        public ?int $statusCode = null,
        public ?string $reason = null,
        public ?int $errorCode = null,
    ) {
    }
}
