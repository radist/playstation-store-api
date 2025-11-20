<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Common;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Qualification;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for Qualification DTO
 */
final class QualificationTest extends TestCase
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

    public function testDeserializeValidQualification(): void
    {
        $json = '{
            "type": "ENTITLEMENT_IN_CART",
            "value": "UP1234-CUSA12345_00-0000000000000000"
        }';

        $qualification = $this->serializer->deserialize($json, Qualification::class, 'json');

        $this->assertInstanceOf(Qualification::class, $qualification);
        $this->assertSame('ENTITLEMENT_IN_CART', $qualification->type);
        $this->assertSame('UP1234-CUSA12345_00-0000000000000000', $qualification->value);
    }

    public function testDeserializeQualificationWithNullValues(): void
    {
        $json = '{
            "type": null,
            "value": null
        }';

        $qualification = $this->serializer->deserialize($json, Qualification::class, 'json');

        $this->assertInstanceOf(Qualification::class, $qualification);
        $this->assertNull($qualification->type);
        $this->assertNull($qualification->value);
    }

    public function testDeserializeQualificationWithPartialData(): void
    {
        $json = '{
            "type": "PS_PLUS"
        }';

        $qualification = $this->serializer->deserialize($json, Qualification::class, 'json');

        $this->assertInstanceOf(Qualification::class, $qualification);
        $this->assertSame('PS_PLUS', $qualification->type);
        $this->assertNull($qualification->value);
    }

    public function testDeserializeQualificationWithDifferentTypes(): void
    {
        $testCases = [
            ['type' => 'ENTITLEMENT_IN_CART', 'value' => 'UP1234-CUSA12345_00-0000000000000000'],
            ['type' => 'PS_PLUS', 'value' => 'ESSENTIAL'],
            ['type' => 'PS_PLUS', 'value' => 'EXTRA'],
            ['type' => 'PS_PLUS', 'value' => 'DELUXE'],
        ];

        foreach ($testCases as $testCase) {
            $json = json_encode($testCase);
            $qualification = $this->serializer->deserialize($json, Qualification::class, 'json');

            $this->assertInstanceOf(Qualification::class, $qualification);
            $this->assertSame($testCase['type'], $qualification->type);
            $this->assertSame($testCase['value'], $qualification->value);
        }
    }
}
