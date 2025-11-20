<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class GameCTAAction
{
    /**
     * @param GameCTAActionParam[]|null $param
     */
    public function __construct(
        public ?string $type = null, // e.g. "ADD_TO_CART"
        /** @var GameCTAActionParam[]|null */
        public ?array $param = null,
    ) {
    }
}
