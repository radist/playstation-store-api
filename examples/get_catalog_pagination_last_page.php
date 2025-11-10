<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\ValueObject\Pagination;
use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;

require_once __DIR__ . '/../vendor/autoload.php';

// Create HTTP client and factory
$httpClient = new GuzzleClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/', 'timeout' => 5]);
$requestFactory = new Psr17Factory();

// Create client with factory
$client = ClientFactory::create(
    RegionEnum::UNITED_STATES,
    $httpClient,
    $requestFactory
);

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
