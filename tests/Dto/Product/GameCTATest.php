<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Product\GameCTA;
use PlaystationStoreApi\Dto\Product\GameCTAAction;
use PlaystationStoreApi\Dto\Product\GameCTAMeta;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for GameCTA DTO
 */
final class GameCTATest extends TestCase
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

    public function testDeserializeGameCTAWithAllFields(): void
    {
        $json = '{
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
        }';

        $gameCTA = $this->serializer->deserialize($json, GameCTA::class, 'json');

        $this->assertInstanceOf(GameCTA::class, $gameCTA);
        $this->assertSame('ADD_TO_CART', $gameCTA->type);
        $this->assertInstanceOf(Price::class, $gameCTA->price);
        $this->assertSame('59.99', $gameCTA->price->basePrice);
        $this->assertInstanceOf(GameCTAAction::class, $gameCTA->action);
        $this->assertSame('ADD_TO_CART', $gameCTA->action->type);
        $this->assertInstanceOf(GameCTAMeta::class, $gameCTA->meta);
        $this->assertFalse($gameCTA->meta->exclusive);
        $this->assertSame('NONE', $gameCTA->meta->upSellService);
    }

    public function testDeserializeGameCTAWithUpsellType(): void
    {
        $json = '{
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
        }';

        $gameCTA = $this->serializer->deserialize($json, GameCTA::class, 'json');

        $this->assertInstanceOf(GameCTA::class, $gameCTA);
        $this->assertSame('UPSELL_PS_PLUS_GAME_CATALOG', $gameCTA->type);
        $this->assertInstanceOf(Price::class, $gameCTA->price);
        $this->assertTrue($gameCTA->price->isFree);
        $this->assertInstanceOf(GameCTAMeta::class, $gameCTA->meta);
        $this->assertTrue($gameCTA->meta->exclusive);
        $this->assertSame('PS_PLUS', $gameCTA->meta->upSellService);
    }

    public function testDeserializeGameCTAWithNullValues(): void
    {
        $json = '{
            "type": null,
            "price": null,
            "action": null,
            "meta": null
        }';

        $gameCTA = $this->serializer->deserialize($json, GameCTA::class, 'json');

        $this->assertInstanceOf(GameCTA::class, $gameCTA);
        $this->assertNull($gameCTA->type);
        $this->assertNull($gameCTA->price);
        $this->assertNull($gameCTA->action);
        $this->assertNull($gameCTA->meta);
    }

    public function testDeserializeGameCTAWithPartialData(): void
    {
        $json = '{
            "type": "ADD_TO_CART"
        }';

        $gameCTA = $this->serializer->deserialize($json, GameCTA::class, 'json');

        $this->assertInstanceOf(GameCTA::class, $gameCTA);
        $this->assertSame('ADD_TO_CART', $gameCTA->type);
        $this->assertNull($gameCTA->price);
        $this->assertNull($gameCTA->action);
        $this->assertNull($gameCTA->meta);
    }
}
