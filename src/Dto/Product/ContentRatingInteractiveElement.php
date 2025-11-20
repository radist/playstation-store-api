<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class ContentRatingInteractiveElement
{
    public function __construct(
        public ?string $description = null, // e.g. "In-Game Purchases"
        public ?string $name = null,
    ) {
    }
}
