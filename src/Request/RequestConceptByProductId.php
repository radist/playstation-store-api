<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Enum\OperationSha256Enum;

/**
 * Request for getting a concept by product ID
 */
final class RequestConceptByProductId implements BaseRequest
{
    public function __construct(public readonly string $productId)
    {
    }

    public function getResponseDtoClass(): string
    {
        return Concept::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::metGetConceptByProductIdQuery->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::metGetConceptByProductIdQuery->value;
    }

    public function getDataPath(): string
    {
        return 'data.conceptRetrieve';
    }
}
