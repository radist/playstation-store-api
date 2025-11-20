<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Serializer;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Enum\CatalogSortingEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Enum\SortingDirectionEnum;
use PlaystationStoreApi\Serializer\EnumNormalizer;

/**
 * Test for EnumNormalizer
 */
final class EnumNormalizerTest extends TestCase
{
    private EnumNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new EnumNormalizer();
    }

    /**
     * Test normalize with string-backed enum returns string value
     */
    public function testNormalizeWithStringBackedEnumReturnsString(): void
    {
        $enum = RegionEnum::UNITED_STATES;
        $result = $this->normalizer->normalize($enum);

        $this->assertIsString($result);
        $this->assertSame('en-us', $result);
    }

    /**
     * Test normalize with int-backed enum returns int value
     */
    public function testNormalizeWithIntBackedEnumReturnsInt(): void
    {
        $enum = SortingDirectionEnum::ASC;
        $result = $this->normalizer->normalize($enum);

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    /**
     * Test normalize with different string enum values
     */
    public function testNormalizeWithDifferentStringEnumValues(): void
    {
        $enum1 = RegionEnum::RUSSIA;
        $result1 = $this->normalizer->normalize($enum1);
        $this->assertSame('ru-ru', $result1);

        $enum2 = CatalogSortingEnum::BESTSELLERS;
        $result2 = $this->normalizer->normalize($enum2);
        $this->assertSame('sales30', $result2);
    }

    /**
     * Test normalize throws exception when object is not BackedEnum
     */
    public function testNormalizeThrowsExceptionWhenObjectIsNotBackedEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected BackedEnum instance');

        $this->normalizer->normalize('not an enum');
    }

    /**
     * Test normalize throws exception when object is not enum at all
     */
    public function testNormalizeThrowsExceptionWhenObjectIsNotEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected BackedEnum instance');

        $this->normalizer->normalize(new \stdClass());
    }

    /**
     * Test normalize throws exception when object is plain enum (not backed)
     */
    public function testNormalizeThrowsExceptionWhenObjectIsPlainEnum(): void
    {
        // Create a plain enum (not backed) for testing
        $plainEnum = new class () {
            // This is not a real enum, but we can test with a mock-like object
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected BackedEnum instance');

        $this->normalizer->normalize($plainEnum);
    }

    /**
     * Test supportsNormalization returns true for string-backed enum
     */
    public function testSupportsNormalizationReturnsTrueForStringBackedEnum(): void
    {
        $enum = RegionEnum::UNITED_STATES;
        $result = $this->normalizer->supportsNormalization($enum);

        $this->assertTrue($result);
    }

    /**
     * Test supportsNormalization returns true for int-backed enum
     */
    public function testSupportsNormalizationReturnsTrueForIntBackedEnum(): void
    {
        $enum = SortingDirectionEnum::ASC;
        $result = $this->normalizer->supportsNormalization($enum);

        $this->assertTrue($result);
    }

    /**
     * Test supportsNormalization returns false for non-enum string
     */
    public function testSupportsNormalizationReturnsFalseForNonEnumString(): void
    {
        $result = $this->normalizer->supportsNormalization('not an enum');

        $this->assertFalse($result);
    }

    /**
     * Test supportsNormalization returns false for non-enum object
     */
    public function testSupportsNormalizationReturnsFalseForNonEnumObject(): void
    {
        $result = $this->normalizer->supportsNormalization(new \stdClass());

        $this->assertFalse($result);
    }

    /**
     * Test supportsNormalization returns false for null
     */
    public function testSupportsNormalizationReturnsFalseForNull(): void
    {
        $result = $this->normalizer->supportsNormalization(null);

        $this->assertFalse($result);
    }

    /**
     * Test supportsNormalization returns false for array
     */
    public function testSupportsNormalizationReturnsFalseForArray(): void
    {
        $result = $this->normalizer->supportsNormalization([]);

        $this->assertFalse($result);
    }

    /**
     * Test supportsNormalization returns false for integer
     */
    public function testSupportsNormalizationReturnsFalseForInteger(): void
    {
        $result = $this->normalizer->supportsNormalization(123);

        $this->assertFalse($result);
    }

    /**
     * Test getSupportedTypes returns BackedEnum class
     */
    public function testGetSupportedTypesReturnsBackedEnumClass(): void
    {
        $result = $this->normalizer->getSupportedTypes('json');

        $this->assertArrayHasKey(\BackedEnum::class, $result);
        $this->assertFalse($result[\BackedEnum::class]);
    }

    /**
     * Test normalize with context parameter (should work regardless of context)
     */
    public function testNormalizeWithContextParameter(): void
    {
        $enum = RegionEnum::UNITED_STATES;
        $result = $this->normalizer->normalize($enum, 'json', ['some' => 'context']);

        $this->assertSame('en-us', $result);
    }

    /**
     * Test supportsNormalization with context parameter (should work regardless of context)
     */
    public function testSupportsNormalizationWithContextParameter(): void
    {
        $enum = CatalogSortingEnum::RELEASE_DATE;
        $result = $this->normalizer->supportsNormalization($enum, 'json', ['some' => 'context']);

        $this->assertTrue($result);
    }

    /**
     * Test normalize with format parameter (should work regardless of format)
     */
    public function testNormalizeWithFormatParameter(): void
    {
        $enum = SortingDirectionEnum::DESC;
        $result = $this->normalizer->normalize($enum, 'xml');

        $this->assertIsInt($result);
        $this->assertSame(0, $result);
    }

    /**
     * Test supportsNormalization with format parameter (should work regardless of format)
     */
    public function testSupportsNormalizationWithFormatParameter(): void
    {
        $enum = RegionEnum::RUSSIA;
        $result = $this->normalizer->supportsNormalization($enum, 'xml');

        $this->assertTrue($result);
    }
}
