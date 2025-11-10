<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Product\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for Product DTO
 */
final class ProductTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        // Configure PropertyInfoExtractor to read PHPDoc annotations
        $phpDocExtractor     = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo        = new PropertyInfoExtractor(
            [$reflectionExtractor], // PropertyListExtractorInterface
            [$phpDocExtractor, $reflectionExtractor], // PropertyTypeExtractorInterface (PhpDocExtractor has priority)
            [$phpDocExtractor], // PropertyDescriptionExtractorInterface
            [$reflectionExtractor], // PropertyAccessExtractorInterface
            [$reflectionExtractor] // PropertyInitializableExtractorInterface
        );

        $objectNormalizer = new ObjectNormalizer(
            null,
            null,
            new PropertyAccessor(),
            $propertyInfo
        );
        $this->serializer = new Serializer([
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            $objectNormalizer
        ], [new JsonEncoder()]);
    }

    public function testDeserializeValidProduct(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game",
            "invariantName": "test-game",
            "platforms": ["PS4", "PS5"],
            "publisherName": "Test Publisher",
            "releaseDate": "2023-01-01T00:00:00Z",
            "storeDisplayClassification": "FULL_GAME",
            "price": {
                "basePrice": "59.99",
                "discountedPrice": "39.99",
                "isFree": false,
                "currencyCode": "USD"
            },
            "media": [
                {
                    "__typename": "Media",
                    "role": "MASTER",
                    "type": "SCREENSHOT",
                    "url": "https://example.com/image.jpg",
                    "media": null
                },
                {
                    "__typename": "Media",
                    "role": "PREVIEW",
                    "type": "VIDEO",
                    "url": "https://example.com/video.mp4",
                    "media": [
                        {
                            "__typename": "Media",
                            "role": "SCREENSHOT",
                            "type": "IMAGE",
                            "url": "https://example.com/frame.jpg",
                            "media": null
                        }
                    ]
                }
            ]
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('CUSA12345_00', $product->id);
        $this->assertSame('Test Game', $product->name);
        $this->assertSame('test-game', $product->invariantName);
        $this->assertSame(['PS4', 'PS5'], $product->platforms);
        $this->assertSame('Test Publisher', $product->publisherName);
        $this->assertInstanceOf(\DateTimeInterface::class, $product->releaseDate);
        $this->assertSame('FULL_GAME', $product->storeDisplayClassification);
        $this->assertInstanceOf(Price::class, $product->price);
        $this->assertIsArray($product->media);
        $this->assertCount(2, $product->media);
        $this->assertContainsOnlyInstancesOf(Media::class, $product->media);
        // Проверяем, что первый элемент - простое изображение
        $this->assertSame('MASTER', $product->media[0]->role);
        $this->assertSame('SCREENSHOT', $product->media[0]->type);
        $this->assertNull($product->media[0]->media);
        // Проверяем, что второй элемент - видео с вложенными кадрами
        $this->assertSame('PREVIEW', $product->media[1]->role);
        $this->assertSame('VIDEO', $product->media[1]->type);
        $this->assertIsArray($product->media[1]->media);
        $this->assertCount(1, $product->media[1]->media);
        $this->assertContainsOnlyInstancesOf(Media::class, $product->media[1]->media);
    }

    public function testDeserializeProductWithNullValues(): void
    {
        $json = '{
            "id": null,
            "name": null,
            "invariantName": null,
            "platforms": null,
            "publisherName": null,
            "releaseDate": null,
            "storeDisplayClassification": null,
            "price": null,
            "media": null
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNull($product->id);
        $this->assertNull($product->name);
        $this->assertNull($product->invariantName);
        $this->assertNull($product->platforms);
        $this->assertNull($product->publisherName);
        $this->assertNull($product->releaseDate);
        $this->assertNull($product->storeDisplayClassification);
        $this->assertNull($product->price);
        $this->assertNull($product->media);
    }

    public function testDeserializeProductWithMinimalData(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game"
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('CUSA12345_00', $product->id);
        $this->assertSame('Test Game', $product->name);
        $this->assertNull($product->invariantName);
        $this->assertNull($product->platforms);
        $this->assertNull($product->publisherName);
        $this->assertNull($product->releaseDate);
        $this->assertNull($product->storeDisplayClassification);
        $this->assertNull($product->price);
        $this->assertNull($product->media);
    }
}
