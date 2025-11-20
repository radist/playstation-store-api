<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

final readonly class Qualification
{
    public function __construct(
        public ?string $type = null, // e.g. "ENTITLEMENT_IN_CART"
        public ?string $value = null,
    ) {
    }
}
