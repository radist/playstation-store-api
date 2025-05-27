<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Enum;

trait EnumFromName
{
    public static function valueFromName(string $name): self
    {
        return constant("self::$name");
    }
}
