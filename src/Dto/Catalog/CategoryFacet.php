<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

/**
 * Represents a filter category (e.g., "Genre", "Price")
 */
final readonly class CategoryFacet
{
    /**
     * @param CategoryFacetValue[]|null $values
     */
    public function __construct(
        public ?string $name = null, // e.g. "conceptGenres"
        public ?string $displayName = null, // e.g. "Genre"
        /** @var CategoryFacetValue[]|null */
        public ?array $values = null,
    ) {
    }
}
