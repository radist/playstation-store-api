<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class ContentRatingDescriptor
{
    public function __construct(
        public ?string $description = null, // e.g. "Blood and Gore"
        public ?string $name = null, // e.g. "ESRB_BLOOD_AND_GORE"
        public ?string $url = null,
    ) {
    }
}
