<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Enum;

trait EnumFromName
{
    /**
     * @phpstan-return static
     */
    public static function valueFromName(string $name): self
    {
        /** @var static */
        return constant("self::$name");
    }
}
