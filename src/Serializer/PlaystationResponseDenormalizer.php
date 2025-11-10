<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Custom denormalizer that unwraps PlayStation Store API responses
 * by extracting nested data before denormalization
 */
final class PlaystationResponseDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        // Only process if data is an array and has 'data' key
        if (!is_array($data) || !isset($data['data'])) {
            return false;
        }

        // Only process if dataPath is provided in context
        if (!isset($context['dataPath']) || !is_string($context['dataPath'])) {
            return false;
        }

        // Only process target DTOs (not wrapper classes)
        // Wrapper classes are simple classes that only contain a 'data' property
        // We check if the type ends with 'Response' but not 'ResponseData' (which are target DTOs)
        // or if it's a simple wrapper pattern like 'ProductResponse', 'ConceptResponse', etc.
        $isSimpleWrapper = (str_ends_with($type, 'Response') && !str_contains($type, 'ResponseData'))
            || (str_ends_with($type, 'ResponseData') && !str_contains($type, 'Retrieve') && !str_contains($type, 'CategoryGrid'));

        return !$isSimpleWrapper;
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!is_array($data) || !isset($data['data'])) {
            throw new \InvalidArgumentException('Data must be an array with "data" key');
        }

        $dataPath = $context['dataPath'] ?? null;
        if (!is_string($dataPath)) {
            throw new \InvalidArgumentException('dataPath must be provided in context');
        }

        // Extract nested data by path (e.g., "data.productRetrieve" -> $data['data']['productRetrieve'])
        $nestedData = $this->extractNestedData($data, $dataPath);

        // If nested data is null, return empty instance
        if ($nestedData === null) {
            if (!class_exists($type)) {
                throw new \InvalidArgumentException("Class {$type} does not exist");
            }
            /** @var class-string $type */
            $className = $type;

            /** @psalm-suppress InvalidStringClass */
            return new $className();
        }

        // Remove dataPath from context to avoid infinite recursion
        $denormalizeContext = $context;
        unset($denormalizeContext['dataPath']);

        // Delegate to the next normalizer in chain for actual denormalization
        return $this->denormalizer->denormalize($nestedData, $type, $format, $denormalizeContext);
    }

    /**
     * Extract nested data from array by path
     *
     * @param array<string, mixed> $data
     * @param string $path Path like "data.productRetrieve"
     * @return mixed
     */
    private function extractNestedData(array $data, string $path): mixed
    {
        // Remove "data." prefix if present
        $path = str_starts_with($path, 'data.') ? substr($path, 5) : $path;

        // Navigate through the path
        $current = $data['data'] ?? null;

        if ($current === null) {
            return null;
        }

        // If path is empty or just "data", return the data object
        if ($path === '') {
            return $current;
        }

        // Split path by dots and navigate
        $parts = explode('.', $path);
        foreach ($parts as $part) {
            if (!is_array($current) || !isset($current[$part])) {
                return null;
            }
            $current = $current[$part];
        }

        return $current;
    }
}
