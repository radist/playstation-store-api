<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class GameCTAMeta
{
    public function __construct(
        public ?bool $exclusive = null,
        public ?bool $preOrder = null,
        public ?string $upSellService = null, // e.g. "PS_PLUS", "NONE"
    ) {
    }
}
