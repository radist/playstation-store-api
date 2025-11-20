<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Product;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Product\GameCTAMeta;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for GameCTAMeta DTO
 */
final class GameCTAMetaTest extends TestCase
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

    public function testDeserializeGameCTAMetaWithAllFields(): void
    {
        $json = '{
            "exclusive": true,
            "preOrder": false,
            "upSellService": "PS_PLUS"
        }';

        $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

        $this->assertInstanceOf(GameCTAMeta::class, $meta);
        $this->assertTrue($meta->exclusive);
        $this->assertFalse($meta->preOrder);
        $this->assertSame('PS_PLUS', $meta->upSellService);
    }

    public function testDeserializeGameCTAMetaWithNoneService(): void
    {
        $json = '{
            "exclusive": false,
            "preOrder": false,
            "upSellService": "NONE"
        }';

        $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

        $this->assertInstanceOf(GameCTAMeta::class, $meta);
        $this->assertFalse($meta->exclusive);
        $this->assertFalse($meta->preOrder);
        $this->assertSame('NONE', $meta->upSellService);
    }

    public function testDeserializeGameCTAMetaWithPreOrder(): void
    {
        $json = '{
            "exclusive": false,
            "preOrder": true,
            "upSellService": "NONE"
        }';

        $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

        $this->assertInstanceOf(GameCTAMeta::class, $meta);
        $this->assertFalse($meta->exclusive);
        $this->assertTrue($meta->preOrder);
        $this->assertSame('NONE', $meta->upSellService);
    }

    public function testDeserializeGameCTAMetaWithNullValues(): void
    {
        $json = '{
            "exclusive": null,
            "preOrder": null,
            "upSellService": null
        }';

        $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

        $this->assertInstanceOf(GameCTAMeta::class, $meta);
        $this->assertNull($meta->exclusive);
        $this->assertNull($meta->preOrder);
        $this->assertNull($meta->upSellService);
    }

    public function testDeserializeGameCTAMetaWithPartialData(): void
    {
        $json = '{
            "exclusive": true
        }';

        $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

        $this->assertInstanceOf(GameCTAMeta::class, $meta);
        $this->assertTrue($meta->exclusive);
        $this->assertNull($meta->preOrder);
        $this->assertNull($meta->upSellService);
    }

    public function testDeserializeGameCTAMetaWithDifferentUpSellServices(): void
    {
        $testCases = [
            ['exclusive' => false, 'preOrder' => false, 'upSellService' => 'NONE'],
            ['exclusive' => true, 'preOrder' => false, 'upSellService' => 'PS_PLUS'],
        ];

        foreach ($testCases as $testCase) {
            $json = json_encode($testCase);
            $meta = $this->serializer->deserialize($json, GameCTAMeta::class, 'json');

            $this->assertInstanceOf(GameCTAMeta::class, $meta);
            $this->assertSame($testCase['exclusive'], $meta->exclusive);
            $this->assertSame($testCase['preOrder'], $meta->preOrder);
            $this->assertSame($testCase['upSellService'], $meta->upSellService);
        }
    }
}
