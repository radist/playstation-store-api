<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Concept;

use PlaystationStoreApi\Dto\Product\Product;

/**
 * Concept information from PlayStation Store
 */
final readonly class Concept
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $invariantName = null,
        public ?Product $defaultProduct = null,
    ) {
    }
}
