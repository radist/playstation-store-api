<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for PHP enums that converts them to their backed values (string/int)
 */
final class EnumNormalizer implements NormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): string|int
    {
        if (! $object instanceof \BackedEnum) {
            throw new \InvalidArgumentException('Expected BackedEnum instance');
        }

        return $object->value;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            \BackedEnum::class => false,
        ];
    }
}

