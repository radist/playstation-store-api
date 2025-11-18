<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Enum\CatalogSortingEnum;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\ValueObject\Pagination;
use PlaystationStoreApi\ValueObject\Sorting;

/**
 * Request for getting a catalog (list of concepts)
 *
 * Note: API returns concepts, not products directly.
 * Each concept contains a products array with related products.
 */
final class RequestCatalog implements BaseRequest
{
    public const DEFAULT_PAGINATION_SIZE = 20;

    /** @var array<string, mixed> */
    public array $filterBy = [];

    /** @var array<string, mixed> */
    public array $facetOptions = [];

    public static function createFromCategory(
        CategoryEnum $categoryEnum,
        ?Pagination $pageArgs = null
    ): RequestCatalog {
        return new self(
            $categoryEnum->value,
            $pageArgs ?? new Pagination(self::DEFAULT_PAGINATION_SIZE),
            Sorting::createFromCatalogSorting(CatalogSortingEnum::RELEASE_DATE)
        );
    }

    public function __construct(
        public readonly string $id,
        public Pagination $pageArgs,
        public readonly Sorting $sortBy
    ) {
    }

    public function createNextPageRequest(): RequestCatalog
    {
        $nextPageRequest = clone $this;
        $nextPageRequest->pageArgs = new Pagination(
            $this->pageArgs->size,
            $this->pageArgs->offset + $this->pageArgs->size
        );

        return $nextPageRequest;
    }

    public function getResponseDtoClass(): string
    {
        return CatalogResponseDataCategoryGridRetrieve::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::categoryGridRetrieve->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::categoryGridRetrieve->value;
    }

    public function getDataPath(): string
    {
        return 'data.categoryGridRetrieve';
    }
}
