<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Enum\OperationSha256Enum;

/**
 * Request for getting concept star rating
 */
final class RequestConceptStarRating implements BaseRequest
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
        return OperationSha256Enum::wcaConceptStarRatingRetrive->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::wcaConceptStarRatingRetrive->value;
    }

    public function getDataPath(): string
    {
        return 'data.conceptRetrieve';
    }
}
