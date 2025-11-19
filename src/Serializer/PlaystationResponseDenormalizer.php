<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Serializer;

use PlaystationStoreApi\Serializer\Attribute\PlaystationApiWrapper;
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
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        // This denormalizer supports all types, but requires supportsDenormalization() check
        return ['*' => false];
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        // Only process if data is an array and has 'data' key
        if (! is_array($data) || ! isset($data['data'])) {
            return false;
        }

        // Only process if dataPath is provided in context
        if (! isset($context['dataPath']) || ! is_string($context['dataPath'])) {
            return false;
        }

        // Extract class name from type (handle arrays like "Product[]" or union types like "Product[]|null")
        $className = $this->extractClassName($type);
        if ($className === null || ! class_exists($className)) {
            return false;
        }

        // Only process target DTOs (not wrapper classes)
        // Wrapper classes are marked with #[PlaystationApiWrapper] attribute
        $reflection = new \ReflectionClass($className);
        $attributes = $reflection->getAttributes(PlaystationApiWrapper::class);
        $isWrapper = count($attributes) > 0;

        return ! $isWrapper;
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (! is_array($data) || ! isset($data['data'])) {
            throw new \InvalidArgumentException('Data must be an array with "data" key');
        }

        $dataPath = $context['dataPath'] ?? null;
        if (! is_string($dataPath)) {
            throw new \InvalidArgumentException('dataPath must be provided in context');
        }

        // Extract nested data by path (e.g., "data.productRetrieve" -> $data['data']['productRetrieve'])
        /** @var array<string, mixed> $dataArray */
        $dataArray = $data;
        $nestedData = $this->extractNestedData($dataArray, $dataPath);

        // Extract class name from type (handle arrays like "Product[]" or union types like "Product[]|null")
        $className = $this->extractClassName($type);
        if ($className === null || ! class_exists($className)) {
            throw new \InvalidArgumentException("Class does not exist in type: {$type}");
        }

        // If nested data is null, return empty instance
        if ($nestedData === null) {
            /** @var class-string $className */
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
            if (! is_array($current) || ! isset($current[$part])) {
                return null;
            }
            $current = $current[$part];
        }

        return $current;
    }

    /**
     * Extract class name from type string, handling arrays and union types
     * Examples: "Product" -> "Product", "Product[]" -> "Product", "Product[]|null" -> "Product"
     *
     * @param string $type Type string from serializer
     * @return string|null Class name or null if not a class type
     */
    private function extractClassName(string $type): ?string
    {
        // Remove array suffix (e.g., "Product[]" -> "Product")
        $type = preg_replace('/\[\]$/', '', $type) ?? $type;

        // Handle union types (e.g., "Product|null" -> "Product")
        // Take the first part before "|"
        if (str_contains($type, '|')) {
            $parts = explode('|', $type);
            $type = trim($parts[0] ?? '');
        }

        // Trim whitespace
        $type = trim($type);

        // Return null if empty or not a valid class name format
        if ($type === '' || preg_match('/^[a-zA-Z_][a-zA-Z0-9_\\\\]*$/', $type) === 0) {
            return null;
        }

        return $type;
    }
}
