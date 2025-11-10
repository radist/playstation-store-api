<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\ValueObject\Pagination;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

$request = RequestProductList::createFromCategory(CategoryEnum::PS5_GAMES);
$firstPageResult = $client->getProductList($request);

// $firstPageResult is now a CatalogResponseDataCategoryGridRetrieve DTO object
if ($firstPageResult->pageInfo && $firstPageResult->pageInfo->totalCount !== null && $firstPageResult->pageInfo->size !== null) {
    $totalCount = $firstPageResult->pageInfo->totalCount;
    $size = $firstPageResult->pageInfo->size;

    // Calculate offset for last page: floor(totalCount / size) * size
    $lastPageOffset = (int)(floor($totalCount / $size) * $size);
    $request->pageArgs = new Pagination($size, $lastPageOffset);
    $lastPageResult = $client->getProductList($request);

    echo json_encode(
        [
            'first page result' => $firstPageResult,
            'last page result' => $lastPageResult,
        ],
        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
}
