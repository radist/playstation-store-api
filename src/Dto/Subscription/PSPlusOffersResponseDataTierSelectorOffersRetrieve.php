<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Subscription;

/**
 * PS Plus tier selector offers retrieve data
 */
final readonly class PSPlusOffersResponseDataTierSelectorOffersRetrieve
{
    /**
     * @param SubscriptionOffer[]|null $offers
     */
    public function __construct(
        /** @var SubscriptionOffer[]|null */
        public ?array $offers = null,
    ) {
    }
}
