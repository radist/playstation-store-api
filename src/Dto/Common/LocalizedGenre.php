<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

final readonly class LocalizedGenre
{
    public function __construct(
        public ?string $value = null,
    ) {
    }
}
