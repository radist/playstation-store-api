<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class Edition
{
    /**
     * @param string[]|null $features
     */
    public function __construct(
        public ?string $name = null,
        public ?string $type = null, // e.g. "OTHER", "STANDARD"
        public ?int $ordering = null,
        /** @var string[]|null */
        public ?array $features = null,
    ) {
    }
}
