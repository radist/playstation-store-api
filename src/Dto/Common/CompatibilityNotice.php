<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

final readonly class CompatibilityNotice
{
    public function __construct(
        public ?string $type = null, // e.g. "NO_OF_PLAYERS", "PS5_VIBRATION"
        public ?string $value = null, // e.g. "1", "OPTIONAL", "true"
    ) {
    }
}
