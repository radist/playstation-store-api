<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Common;

/**
 * Price information for a product (SkuPrice)
 */
final readonly class Price
{
    /**
     * @param string[]|null $serviceBranding
     * @param string[]|null $upsellServiceBranding
     * @param Qualification[]|null $qualifications
     */
    public function __construct(
        public ?string $__typename = null,
        public ?string $basePrice = null,
        public ?string $discountText = null,
        public ?string $discountedPrice = null,
        public ?bool $includesBundleOffer = null,
        public ?bool $isExclusive = null,
        public ?bool $isFree = null,
        public ?bool $isTiedToSubscription = null,
        /** @var string[]|null */
        public ?array $serviceBranding = null,
        /** @var string[]|null */
        public ?array $upsellServiceBranding = null,
        public ?string $upsellText = null,
        public ?string $currencyCode = null,

        // Added fields based on JSON
        public ?int $basePriceValue = null,
        public ?int $discountedValue = null,
        public ?string $skuId = null,
        public ?string $rewardId = null,
        public ?string $campaignId = null,
        public ?string $endTime = null,
        /** @var Qualification[]|null */
        public ?array $qualifications = null,
    ) {
    }
}
