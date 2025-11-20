<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Concept;

use PlaystationStoreApi\Dto\Common\CompatibilityNotice;
use PlaystationStoreApi\Dto\Common\Description;
use PlaystationStoreApi\Dto\Common\LocalizedGenre;
use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\PersonalizedMeta;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Common\ReleaseDateDescriptor;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Concept information from PlayStation Store
 */
final readonly class Concept
{
    /**
     * @param Media[]|null $media
     * @param Product[]|null $products
     * @param LocalizedGenre[]|null $combinedLocalizedGenres
     * @param Description[]|null $descriptions
     * @param CompatibilityNotice[]|null $compatibilityNotices
     */
    public function __construct(
        public ?string $__typename = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?string $invariantName = null,

        // Publisher info
        public ?string $publisherName = null,

        // Release date in Concept is an object, unlike in Product
        public ?ReleaseDateDescriptor $releaseDate = null,

        /** @var Media[]|null */
        public ?array $media = null,
        public ?PersonalizedMeta $personalizedMeta = null,
        public ?Price $price = null,

        // Lists of products (can be null in some queries)
        /** @var Product[]|null */
        public ?array $products = null,

        // The main product associated with this concept
        public ?Product $defaultProduct = null,

        /** @var LocalizedGenre[]|null */
        public ?array $combinedLocalizedGenres = null,

        /** @var Description[]|null */
        public ?array $descriptions = null,

        /** @var CompatibilityNotice[]|null */
        public ?array $compatibilityNotices = null,

        // Added fields based on JSON
        public ?bool $isInWishlist = null,
        public ?bool $isWishlistable = null,
        public ?SelectableProducts $selectableProducts = null,
    ) {
    }
}
