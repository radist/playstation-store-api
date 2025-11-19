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
        try {
            /** @var static */
            return constant("self::$name");
        } catch (\Error $e) {
            throw new \ValueError("Unknown enum case: $name", 0, $e);
        }
    }
}
