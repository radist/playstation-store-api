<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Concept;

use PlaystationStoreApi\Dto\Product\Product;

final readonly class SelectableProducts
{
    /**
     * @param Product[]|null $purchasableProducts
     */
    public function __construct(
        /** @var Product[]|null */
        public ?array $purchasableProducts = null,
    ) {
    }
}
