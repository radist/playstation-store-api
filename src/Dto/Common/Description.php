<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Handles descriptions like "LONG" (overview), "LEGAL", etc.
 */
final readonly class Description
{
    public function __construct(
        public ?string $type = null,
        public ?string $value = null,
    ) {
    }
}
