<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Error object containing error information
 */
final readonly class ErrorObject
{
    public function __construct(
        public ?string $message = null,
        public ?array $path = null,
        public ?ErrorObjectExtensions $extensions = null,
    ) {
    }
}
