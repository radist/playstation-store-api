<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Price information for a product
 */
final readonly class Price
{
    public function __construct(
        public ?string $basePrice = null,
        public ?string $discountedPrice = null,
        public ?bool $isFree = null,
        public ?string $currencyCode = null,
    ) {
    }
}
