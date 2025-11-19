<?php

declare(strict_types=1);

namespace PlaystationStoreApi\ValueObject;

final class Pagination
{
    public function __construct(public readonly int $size, public readonly int $offset = 0)
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Size must be greater than 0, got: ' . $size);
        }

        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset must be greater than or equal to 0, got: ' . $offset);
        }
    }
}
