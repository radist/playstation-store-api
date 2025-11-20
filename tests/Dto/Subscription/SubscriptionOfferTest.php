<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Subscription;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Subscription\SubscriptionOffer;
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
 * Test for SubscriptionOffer DTO
 */
final class SubscriptionOfferTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        // Configure PropertyInfoExtractor to read PHPDoc annotations
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
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            $objectNormalizer,
        ], [new JsonEncoder()]);
    }

    public function testDeserializeSubscriptionOfferWithPrice(): void
    {
        $json = '{
            "title": "PlayStation Plus Essential",
            "description": "12 Month Subscription",
            "skuId": "UP9000-PCSE00000_00-0000000000000001",
            "price": {
                "__typename": "SkuPrice",
                "basePrice": "59.99",
                "discountedPrice": "49.99",
                "isFree": false,
                "currencyCode": "USD",
                "serviceBranding": ["PS_PLUS"],
                "upsellServiceBranding": null
            }
        }';

        $offer = $this->serializer->deserialize($json, SubscriptionOffer::class, 'json');

        $this->assertInstanceOf(SubscriptionOffer::class, $offer);
        $this->assertSame('PlayStation Plus Essential', $offer->title);
        $this->assertSame('12 Month Subscription', $offer->description);
        $this->assertSame('UP9000-PCSE00000_00-0000000000000001', $offer->skuId);
        $this->assertInstanceOf(Price::class, $offer->price);
        $this->assertSame('SkuPrice', $offer->price->__typename);
        $this->assertSame('59.99', $offer->price->basePrice);
        $this->assertSame('49.99', $offer->price->discountedPrice);
        $this->assertFalse($offer->price->isFree);
        $this->assertSame('USD', $offer->price->currencyCode);
        $this->assertIsArray($offer->price->serviceBranding);
        $this->assertSame(['PS_PLUS'], $offer->price->serviceBranding);
    }

    public function testDeserializeSubscriptionOfferWithComplexPrice(): void
    {
        $json = '{
            "title": "PlayStation Plus Extra",
            "description": "12 Month Subscription with Game Catalog",
            "skuId": "UP9000-PCSE00000_00-0000000000000002",
            "price": {
                "__typename": "SkuPrice",
                "basePrice": "99.99",
                "discountedPrice": "79.99",
                "discountText": "Save 20%",
                "isFree": false,
                "isExclusive": true,
                "isTiedToSubscription": true,
                "currencyCode": "USD",
                "serviceBranding": ["PS_PLUS", "PS_PLUS_EXTRA"],
                "upsellServiceBranding": ["PS_PLUS_PREMIUM"],
                "upsellText": "Upgrade to Premium"
            }
        }';

        $offer = $this->serializer->deserialize($json, SubscriptionOffer::class, 'json');

        $this->assertInstanceOf(SubscriptionOffer::class, $offer);
        $this->assertInstanceOf(Price::class, $offer->price);
        $this->assertSame('99.99', $offer->price->basePrice);
        $this->assertSame('79.99', $offer->price->discountedPrice);
        $this->assertSame('Save 20%', $offer->price->discountText);
        $this->assertTrue($offer->price->isExclusive);
        $this->assertTrue($offer->price->isTiedToSubscription);
        $this->assertIsArray($offer->price->serviceBranding);
        $this->assertCount(2, $offer->price->serviceBranding);
        $this->assertSame(['PS_PLUS', 'PS_PLUS_EXTRA'], $offer->price->serviceBranding);
        $this->assertIsArray($offer->price->upsellServiceBranding);
        $this->assertSame(['PS_PLUS_PREMIUM'], $offer->price->upsellServiceBranding);
        $this->assertSame('Upgrade to Premium', $offer->price->upsellText);
    }

    public function testDeserializeSubscriptionOfferWithNullPrice(): void
    {
        $json = '{
            "title": "Free Trial",
            "description": "7 Day Free Trial",
            "skuId": "UP9000-PCSE00000_00-0000000000000003",
            "price": null
        }';

        $offer = $this->serializer->deserialize($json, SubscriptionOffer::class, 'json');

        $this->assertInstanceOf(SubscriptionOffer::class, $offer);
        $this->assertSame('Free Trial', $offer->title);
        $this->assertNull($offer->price);
    }

    public function testDeserializeSubscriptionOfferWithFreePrice(): void
    {
        $json = '{
            "title": "Free Offer",
            "description": "Free Subscription",
            "skuId": "UP9000-PCSE00000_00-0000000000000004",
            "price": {
                "__typename": "SkuPrice",
                "basePrice": "0.00",
                "discountedPrice": "0.00",
                "isFree": true,
                "currencyCode": "USD"
            }
        }';

        $offer = $this->serializer->deserialize($json, SubscriptionOffer::class, 'json');

        $this->assertInstanceOf(SubscriptionOffer::class, $offer);
        $this->assertInstanceOf(Price::class, $offer->price);
        $this->assertTrue($offer->price->isFree);
        $this->assertSame('0.00', $offer->price->basePrice);
    }

    public function testDeserializeSubscriptionOfferWithMinimalData(): void
    {
        $json = '{
            "title": "Basic Offer"
        }';

        $offer = $this->serializer->deserialize($json, SubscriptionOffer::class, 'json');

        $this->assertInstanceOf(SubscriptionOffer::class, $offer);
        $this->assertSame('Basic Offer', $offer->title);
        $this->assertNull($offer->description);
        $this->assertNull($offer->skuId);
        $this->assertNull($offer->price);
    }
}
