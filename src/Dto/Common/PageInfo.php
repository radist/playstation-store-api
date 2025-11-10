<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Pagination information for API responses
 */
final readonly class PageInfo
{
    public function __construct(
        public ?int $totalCount = null,
        public ?bool $isLast = null,
        public ?int $offset = null,
        public ?int $size = null,
    ) {
    }
}
