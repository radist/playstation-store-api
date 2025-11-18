<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Personalized metadata for a concept
 */
final readonly class PersonalizedMeta
{
    /**
     * @param Media[]|null $media
     */
    public function __construct(
        public ?string $__typename = null,
        public ?bool $hasMediaOverrides = null,
        /** @var Media[]|null */
        public ?array $media = null,
    ) {
    }
}
