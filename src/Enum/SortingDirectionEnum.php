<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Enum;

enum SortingDirectionEnum: int
{
    use EnumFromName;

    case ASC = 1;

    case DESC = 0;
}
