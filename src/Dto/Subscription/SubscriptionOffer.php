<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Subscription;

use PlaystationStoreApi\Dto\Common\Price;

/**
 * PlayStation Plus subscription offer information
 */
final readonly class SubscriptionOffer
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $skuId = null,
        public ?Price $price = null,
    ) {
    }
}
