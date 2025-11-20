<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

/**
 * Represents a single value within a filter (e.g., "Action" with count 98)
 */
final readonly class CategoryFacetValue
{
    public function __construct(
        public ?string $key = null, // e.g. "ACTION" or "0-199"
        public ?string $displayName = null, // e.g. "Action" or "Under $1.99"
        public ?int $count = null,
    ) {
    }
}
