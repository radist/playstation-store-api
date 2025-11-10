<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\Price;

/**
 * Product information from PlayStation Store
 */
final readonly class Product
{
    /**
     * @param Media[]|null $media
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $invariantName = null,
        public ?array $platforms = null,
        public ?string $publisherName = null,
        public ?\DateTimeInterface $releaseDate = null,
        public ?string $storeDisplayClassification = null,
        public ?Price $price = null,
        /** @var Media[]|null */
        public ?array $media = null,
    ) {
    }
}
