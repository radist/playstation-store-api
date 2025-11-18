<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Concept;

use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\PersonalizedMeta;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Concept information from PlayStation Store
 */
final readonly class Concept
{
    /**
     * @param Media[]|null $media
     * @param Product[]|null $products
     */
    public function __construct(
        public ?string $__typename = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?string $invariantName = null,
        /** @var Media[]|null */
        public ?array $media = null,
        public ?PersonalizedMeta $personalizedMeta = null,
        public ?Price $price = null,
        /** @var Product[]|null */
        public ?array $products = null,
        public ?Product $defaultProduct = null,
    ) {
    }
}
