<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

use PlaystationStoreApi\Dto\Common\Price;

final readonly class GameCTA
{
    public function __construct(
        public ?string $type = null, // e.g. "ADD_TO_CART", "UPSELL_PS_PLUS_GAME_CATALOG"
        public ?Price $price = null,
        public ?GameCTAAction $action = null,
        public ?GameCTAMeta $meta = null,
    ) {
    }
}
