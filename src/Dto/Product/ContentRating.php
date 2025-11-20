<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class ContentRating
{
    /**
     * @param ContentRatingDescriptor[]|null $descriptors
     * @param ContentRatingInteractiveElement[]|null $interactiveElements
     */
    public function __construct(
        public ?string $authority = null, // e.g. "ESRB"
        public ?string $description = null, // e.g. "ESRB Mature"
        public ?string $name = null, // e.g. "ESRB_MATURE"
        public ?string $url = null, // Image URL
        /** @var ContentRatingDescriptor[]|null */
        public ?array $descriptors = null,
        /** @var ContentRatingInteractiveElement[]|null */
        public ?array $interactiveElements = null,
    ) {
    }
}
