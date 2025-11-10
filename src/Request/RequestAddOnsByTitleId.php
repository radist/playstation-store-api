<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\AddOns\AddOnsResponseDataAddOnProductsByTitleIdRetrieve;
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\ValueObject\Pagination;

/**
 * Request for getting add-ons by title ID
 */
final class RequestAddOnsByTitleId implements BaseRequest
{
    public const DEFAULT_PAGINATION_SIZE = 20;

    public readonly Pagination $pageArgs;

    public function __construct(
        public readonly string $npTitleId,
        ?Pagination $pageArgs = null
    ) {
        $this->pageArgs = $pageArgs ?? new Pagination(self::DEFAULT_PAGINATION_SIZE);
    }

    public function getResponseDtoClass(): string
    {
        return AddOnsResponseDataAddOnProductsByTitleIdRetrieve::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::metGetAddOnsByTitleId->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::metGetAddOnsByTitleId->value;
    }

    public function getDataPath(): string
    {
        return 'data.addOnProductsByTitleIdRetrieve';
    }
}
