<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

final readonly class CategorySortingOption
{
    public function __construct(
        public ?string $name = null, // e.g. "productReleaseDate"
        public ?string $displayName = null, // e.g. "Release Date (New - Old)"
        public ?bool $isAscending = null,
    ) {
    }
}
