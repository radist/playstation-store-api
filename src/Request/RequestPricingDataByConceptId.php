<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Enum\OperationSha256Enum;

/**
 * Request for getting pricing data by concept ID
 */
final class RequestPricingDataByConceptId implements BaseRequest
{
    public function __construct(public readonly string $conceptId)
    {
    }

    public function getResponseDtoClass(): string
    {
        return Concept::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::metGetPricingDataByConceptId->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::metGetPricingDataByConceptId->value;
    }

    public function getDataPath(): string
    {
        return 'data.conceptRetrieve';
    }
}
