<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Media information for a product
 */
final readonly class Media
{
    /**
     * @param Media[]|null $media Вложенные медиа-объекты (например, кадры для видео)
     */
    public function __construct(
        public ?string $__typename = null,
        public ?string $role = null,
        public ?string $type = null,
        public ?string $url = null,
        /** @var Media[]|null */
        public ?array $media = null,
    ) {
    }
}
