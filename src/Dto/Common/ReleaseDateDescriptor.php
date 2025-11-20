<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Represents the release date object found in Concepts (unlike the simple string in Products)
 */
final readonly class ReleaseDateDescriptor
{
    public function __construct(
        public ?string $type = null, // e.g. "DAY_MONTH_YEAR"
        public ?string $value = null, // e.g. "2022-09-02T04:00:00Z"
    ) {
    }
}
