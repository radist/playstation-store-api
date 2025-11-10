<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Common;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Price;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for Price DTO
 */
final class PriceTest extends TestCase
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

    public function testDeserializeValidPrice(): void
    {
        $json = '{
            "basePrice": "29.99",
            "discountedPrice": "19.99",
            "isFree": false,
            "currencyCode": "USD"
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('29.99', $price->basePrice);
        $this->assertSame('19.99', $price->discountedPrice);
        $this->assertFalse($price->isFree);
        $this->assertSame('USD', $price->currencyCode);
    }

    public function testDeserializePriceWithNullValues(): void
    {
        $json = '{
            "basePrice": null,
            "discountedPrice": null,
            "isFree": null,
            "currencyCode": null
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertNull($price->basePrice);
        $this->assertNull($price->discountedPrice);
        $this->assertNull($price->isFree);
        $this->assertNull($price->currencyCode);
    }

    public function testDeserializePriceWithPartialData(): void
    {
        $json = '{
            "basePrice": "29.99",
            "currencyCode": "EUR"
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('29.99', $price->basePrice);
        $this->assertNull($price->discountedPrice);
        $this->assertNull($price->isFree);
        $this->assertSame('EUR', $price->currencyCode);
    }

    public function testDeserializeFreePrice(): void
    {
        $json = '{
            "basePrice": "0.00",
            "discountedPrice": "0.00",
            "isFree": true,
            "currencyCode": "USD"
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('0.00', $price->basePrice);
        $this->assertSame('0.00', $price->discountedPrice);
        $this->assertTrue($price->isFree);
        $this->assertSame('USD', $price->currencyCode);
    }
}
