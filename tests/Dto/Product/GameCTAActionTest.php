<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Product\GameCTAAction;
use PlaystationStoreApi\Dto\Product\GameCTAActionParam;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for GameCTAAction DTO
 */
final class GameCTAActionTest extends TestCase
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

    public function testDeserializeGameCTAActionWithParams(): void
    {
        $json = '{
            "type": "ADD_TO_CART",
            "param": [
                {
                    "name": "skuId",
                    "values": ["UP1234-CUSA12345_00-0000000000000000"]
                },
                {
                    "name": "rewardId",
                    "values": ["REWARD123"]
                }
            ]
        }';

        $action = $this->serializer->deserialize($json, GameCTAAction::class, 'json');

        $this->assertInstanceOf(GameCTAAction::class, $action);
        $this->assertSame('ADD_TO_CART', $action->type);
        $this->assertIsArray($action->param);
        $this->assertCount(2, $action->param);
        $this->assertContainsOnlyInstancesOf(GameCTAActionParam::class, $action->param);
        $this->assertSame('skuId', $action->param[0]->name);
        $this->assertSame(['UP1234-CUSA12345_00-0000000000000000'], $action->param[0]->values);
        $this->assertSame('rewardId', $action->param[1]->name);
        $this->assertSame(['REWARD123'], $action->param[1]->values);
    }

    public function testDeserializeGameCTAActionWithEmptyParams(): void
    {
        $json = '{
            "type": "UPSELL_PS_PLUS_GAME_CATALOG",
            "param": []
        }';

        $action = $this->serializer->deserialize($json, GameCTAAction::class, 'json');

        $this->assertInstanceOf(GameCTAAction::class, $action);
        $this->assertSame('UPSELL_PS_PLUS_GAME_CATALOG', $action->type);
        $this->assertIsArray($action->param);
        $this->assertEmpty($action->param);
    }

    public function testDeserializeGameCTAActionWithNullValues(): void
    {
        $json = '{
            "type": null,
            "param": null
        }';

        $action = $this->serializer->deserialize($json, GameCTAAction::class, 'json');

        $this->assertInstanceOf(GameCTAAction::class, $action);
        $this->assertNull($action->type);
        $this->assertNull($action->param);
    }

    public function testDeserializeGameCTAActionWithPartialData(): void
    {
        $json = '{
            "type": "ADD_TO_CART"
        }';

        $action = $this->serializer->deserialize($json, GameCTAAction::class, 'json');

        $this->assertInstanceOf(GameCTAAction::class, $action);
        $this->assertSame('ADD_TO_CART', $action->type);
        $this->assertNull($action->param);
    }
}
