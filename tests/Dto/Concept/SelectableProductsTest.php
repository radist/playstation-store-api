<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Concept;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Concept\SelectableProducts;
use PlaystationStoreApi\Dto\Product\Product;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for SelectableProducts DTO
 */
final class SelectableProductsTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );

        $objectNormalizer = new ObjectNormalizer(
            null,
            null,
            new PropertyAccessor(),
            $propertyInfo
        );
        $this->serializer = new Serializer([
            new ArrayDenormalizer(),
            $objectNormalizer,
        ], [new JsonEncoder()]);
    }

    public function testDeserializeSelectableProductsWithProducts(): void
    {
        $json = '{
            "purchasableProducts": [
                {
                    "id": "UP1234-CUSA12345_00-0000000000000000",
                    "name": "Test Product 1",
                    "storeDisplayClassification": "FULL_GAME"
                },
                {
                    "id": "UP1234-CUSA12345_00-0000000000000001",
                    "name": "Test Product 2",
                    "storeDisplayClassification": "ADD_ON"
                }
            ]
        }';

        $selectableProducts = $this->serializer->deserialize($json, SelectableProducts::class, 'json');

        $this->assertInstanceOf(SelectableProducts::class, $selectableProducts);
        $this->assertIsArray($selectableProducts->purchasableProducts);
        $this->assertCount(2, $selectableProducts->purchasableProducts);
        $this->assertContainsOnlyInstancesOf(Product::class, $selectableProducts->purchasableProducts);
        $this->assertSame('UP1234-CUSA12345_00-0000000000000000', $selectableProducts->purchasableProducts[0]->id);
        $this->assertSame('Test Product 1', $selectableProducts->purchasableProducts[0]->name);
        $this->assertSame('UP1234-CUSA12345_00-0000000000000001', $selectableProducts->purchasableProducts[1]->id);
        $this->assertSame('Test Product 2', $selectableProducts->purchasableProducts[1]->name);
    }

    public function testDeserializeSelectableProductsWithNullProducts(): void
    {
        $json = '{
            "purchasableProducts": null
        }';

        $selectableProducts = $this->serializer->deserialize($json, SelectableProducts::class, 'json');

        $this->assertInstanceOf(SelectableProducts::class, $selectableProducts);
        $this->assertNull($selectableProducts->purchasableProducts);
    }

    public function testDeserializeSelectableProductsWithEmptyProducts(): void
    {
        $json = '{
            "purchasableProducts": []
        }';

        $selectableProducts = $this->serializer->deserialize($json, SelectableProducts::class, 'json');

        $this->assertInstanceOf(SelectableProducts::class, $selectableProducts);
        $this->assertIsArray($selectableProducts->purchasableProducts);
        $this->assertEmpty($selectableProducts->purchasableProducts);
    }
}
