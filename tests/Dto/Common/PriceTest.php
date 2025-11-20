<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Common;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Common\Qualification;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
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
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );

        $this->serializer = new Serializer([
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                null,
                null,
                PropertyAccess::createPropertyAccessor(),
                $propertyInfo
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

    public function testDeserializePriceWithNewFields(): void
    {
        $json = '{
            "basePrice": "29.99",
            "discountedPrice": "19.99",
            "basePriceValue": 2999,
            "discountedValue": 1999,
            "skuId": "UP1234-CUSA12345_00-0000000000000000",
            "rewardId": "REWARD123",
            "campaignId": "CAMPAIGN456",
            "endTime": "2024-12-31T23:59:59Z",
            "currencyCode": "USD"
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('29.99', $price->basePrice);
        $this->assertSame('19.99', $price->discountedPrice);
        $this->assertSame(2999, $price->basePriceValue);
        $this->assertSame(1999, $price->discountedValue);
        $this->assertSame('UP1234-CUSA12345_00-0000000000000000', $price->skuId);
        $this->assertSame('REWARD123', $price->rewardId);
        $this->assertSame('CAMPAIGN456', $price->campaignId);
        $this->assertSame('2024-12-31T23:59:59Z', $price->endTime);
        $this->assertSame('USD', $price->currencyCode);
    }

    public function testDeserializePriceWithQualifications(): void
    {
        $json = '{
            "basePrice": "29.99",
            "discountedPrice": "19.99",
            "currencyCode": "USD",
            "qualifications": [
                {
                    "type": "ENTITLEMENT_IN_CART",
                    "value": "UP1234-CUSA12345_00-0000000000000000"
                },
                {
                    "type": "PS_PLUS",
                    "value": "ESSENTIAL"
                }
            ]
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertIsArray($price->qualifications);
        $this->assertCount(2, $price->qualifications);
        $this->assertContainsOnlyInstancesOf(Qualification::class, $price->qualifications);
        $this->assertSame('ENTITLEMENT_IN_CART', $price->qualifications[0]->type);
        $this->assertSame('UP1234-CUSA12345_00-0000000000000000', $price->qualifications[0]->value);
        $this->assertSame('PS_PLUS', $price->qualifications[1]->type);
        $this->assertSame('ESSENTIAL', $price->qualifications[1]->value);
    }

    public function testDeserializePriceWithNullNewFields(): void
    {
        $json = '{
            "basePrice": "29.99",
            "basePriceValue": null,
            "discountedValue": null,
            "skuId": null,
            "rewardId": null,
            "campaignId": null,
            "endTime": null,
            "qualifications": null
        }';

        $price = $this->serializer->deserialize($json, Price::class, 'json');

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('29.99', $price->basePrice);
        $this->assertNull($price->basePriceValue);
        $this->assertNull($price->discountedValue);
        $this->assertNull($price->skuId);
        $this->assertNull($price->rewardId);
        $this->assertNull($price->campaignId);
        $this->assertNull($price->endTime);
        $this->assertNull($price->qualifications);
    }
}
