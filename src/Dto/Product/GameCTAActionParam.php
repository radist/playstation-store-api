<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

final readonly class GameCTAActionParam
{
    /**
     * @param string[]|null $values
     */
    public function __construct(
        public ?string $name = null, // e.g. "skuId", "rewardId"
        /** @var string[]|null */
        public ?array $values = null,
    ) {
    }
}
