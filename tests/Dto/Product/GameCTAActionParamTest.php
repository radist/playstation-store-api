<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Product\GameCTAActionParam;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for GameCTAActionParam DTO
 */
final class GameCTAActionParamTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer([
            new ObjectNormalizer(
                null,
                null,
                PropertyAccess::createPropertyAccessor()
            ),
        ], [new JsonEncoder()]);
    }

    public function testDeserializeGameCTAActionParamWithValues(): void
    {
        $json = '{
            "name": "skuId",
            "values": ["UP1234-CUSA12345_00-0000000000000000"]
        }';

        $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

        $this->assertInstanceOf(GameCTAActionParam::class, $param);
        $this->assertSame('skuId', $param->name);
        $this->assertIsArray($param->values);
        $this->assertCount(1, $param->values);
        $this->assertSame(['UP1234-CUSA12345_00-0000000000000000'], $param->values);
    }

    public function testDeserializeGameCTAActionParamWithMultipleValues(): void
    {
        $json = '{
            "name": "rewardId",
            "values": ["REWARD123", "REWARD456", "REWARD789"]
        }';

        $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

        $this->assertInstanceOf(GameCTAActionParam::class, $param);
        $this->assertSame('rewardId', $param->name);
        $this->assertIsArray($param->values);
        $this->assertCount(3, $param->values);
        $this->assertSame(['REWARD123', 'REWARD456', 'REWARD789'], $param->values);
    }

    public function testDeserializeGameCTAActionParamWithEmptyValues(): void
    {
        $json = '{
            "name": "skuId",
            "values": []
        }';

        $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

        $this->assertInstanceOf(GameCTAActionParam::class, $param);
        $this->assertSame('skuId', $param->name);
        $this->assertIsArray($param->values);
        $this->assertEmpty($param->values);
    }

    public function testDeserializeGameCTAActionParamWithNullValues(): void
    {
        $json = '{
            "name": null,
            "values": null
        }';

        $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

        $this->assertInstanceOf(GameCTAActionParam::class, $param);
        $this->assertNull($param->name);
        $this->assertNull($param->values);
    }

    public function testDeserializeGameCTAActionParamWithPartialData(): void
    {
        $json = '{
            "name": "skuId"
        }';

        $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

        $this->assertInstanceOf(GameCTAActionParam::class, $param);
        $this->assertSame('skuId', $param->name);
        $this->assertNull($param->values);
    }

    public function testDeserializeGameCTAActionParamWithDifferentNames(): void
    {
        $testCases = [
            ['name' => 'skuId', 'values' => ['UP1234-CUSA12345_00-0000000000000000']],
            ['name' => 'rewardId', 'values' => ['REWARD123']],
            ['name' => 'campaignId', 'values' => ['CAMPAIGN456']],
        ];

        foreach ($testCases as $testCase) {
            $json = json_encode($testCase);
            $param = $this->serializer->deserialize($json, GameCTAActionParam::class, 'json');

            $this->assertInstanceOf(GameCTAActionParam::class, $param);
            $this->assertSame($testCase['name'], $param->name);
            $this->assertSame($testCase['values'], $param->values);
        }
    }
}
