<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Subscription\PSPlusOffersResponseDataTierSelectorOffersRetrieve;
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\Enum\PSPlusTierEnum;

/**
 * Request for getting PS Plus tier offers
 */
final class RequestPSPlusTier implements BaseRequest
{
    public function __construct(public readonly PSPlusTierEnum $tierLabel)
    {
    }

    public function getResponseDtoClass(): string
    {
        return PSPlusOffersResponseDataTierSelectorOffersRetrieve::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::featuresRetrieve->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::featuresRetrieve->value;
    }

    public function getDataPath(): string
    {
        return 'data.tierSelectorOffersRetrieve';
    }
}
