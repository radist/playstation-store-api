<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Product\GameCTA;
use PlaystationStoreApi\Dto\Product\Product;
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
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
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
            $objectNormalizer,
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

    public function testDeserializeProductWithWishlistFields(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game",
            "isInWishlist": true,
            "isWishlistable": true
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertTrue($product->isInWishlist);
        $this->assertTrue($product->isWishlistable);
    }

    public function testDeserializeProductWithTypeFields(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game",
            "subType": "FULL_GAME",
            "topCategory": "GAME",
            "type": "GAME"
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('FULL_GAME', $product->subType);
        $this->assertSame('GAME', $product->topCategory);
        $this->assertSame('GAME', $product->type);
    }

    public function testDeserializeProductWithMobileCTAs(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game",
            "mobilectas": [
                {
                    "type": "ADD_TO_CART",
                    "price": {
                        "basePrice": "59.99",
                        "discountedPrice": "39.99",
                        "currencyCode": "USD"
                    },
                    "action": {
                        "type": "ADD_TO_CART",
                        "param": [
                            {
                                "name": "skuId",
                                "values": ["UP1234-CUSA12345_00-0000000000000000"]
                            }
                        ]
                    },
                    "meta": {
                        "exclusive": false,
                        "preOrder": false,
                        "upSellService": "NONE"
                    }
                },
                {
                    "type": "UPSELL_PS_PLUS_GAME_CATALOG",
                    "price": {
                        "basePrice": "0.00",
                        "isFree": true,
                        "currencyCode": "USD"
                    },
                    "action": {
                        "type": "UPSELL_PS_PLUS_GAME_CATALOG",
                        "param": []
                    },
                    "meta": {
                        "exclusive": true,
                        "preOrder": false,
                        "upSellService": "PS_PLUS"
                    }
                }
            ]
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertIsArray($product->mobilectas);
        $this->assertCount(2, $product->mobilectas);
        $this->assertContainsOnlyInstancesOf(GameCTA::class, $product->mobilectas);

        // Test first CTA
        $firstCTA = $product->mobilectas[0];
        $this->assertSame('ADD_TO_CART', $firstCTA->type);
        $this->assertInstanceOf(Price::class, $firstCTA->price);
        $this->assertSame('59.99', $firstCTA->price->basePrice);
        $this->assertSame('39.99', $firstCTA->price->discountedPrice);
        $this->assertInstanceOf(\PlaystationStoreApi\Dto\Product\GameCTAAction::class, $firstCTA->action);
        $this->assertSame('ADD_TO_CART', $firstCTA->action->type);
        $this->assertIsArray($firstCTA->action->param);
        $this->assertCount(1, $firstCTA->action->param);
        $this->assertInstanceOf(\PlaystationStoreApi\Dto\Product\GameCTAActionParam::class, $firstCTA->action->param[0]);
        $this->assertSame('skuId', $firstCTA->action->param[0]->name);
        $this->assertSame(['UP1234-CUSA12345_00-0000000000000000'], $firstCTA->action->param[0]->values);
        $this->assertInstanceOf(\PlaystationStoreApi\Dto\Product\GameCTAMeta::class, $firstCTA->meta);
        $this->assertFalse($firstCTA->meta->exclusive);
        $this->assertFalse($firstCTA->meta->preOrder);
        $this->assertSame('NONE', $firstCTA->meta->upSellService);

        // Test second CTA
        $secondCTA = $product->mobilectas[1];
        $this->assertSame('UPSELL_PS_PLUS_GAME_CATALOG', $secondCTA->type);
        $this->assertInstanceOf(Price::class, $secondCTA->price);
        $this->assertSame('0.00', $secondCTA->price->basePrice);
        $this->assertTrue($secondCTA->price->isFree);
        $this->assertInstanceOf(\PlaystationStoreApi\Dto\Product\GameCTAMeta::class, $secondCTA->meta);
        $this->assertTrue($secondCTA->meta->exclusive);
        $this->assertSame('PS_PLUS', $secondCTA->meta->upSellService);
    }

    public function testDeserializeProductWithNullNewFields(): void
    {
        $json = '{
            "id": "CUSA12345_00",
            "name": "Test Game",
            "isInWishlist": null,
            "isWishlistable": null,
            "mobilectas": null,
            "subType": null,
            "topCategory": null,
            "type": null
        }';

        $product = $this->serializer->deserialize($json, Product::class, 'json');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNull($product->isInWishlist);
        $this->assertNull($product->isWishlistable);
        $this->assertNull($product->mobilectas);
        $this->assertNull($product->subType);
        $this->assertNull($product->topCategory);
        $this->assertNull($product->type);
    }
}
