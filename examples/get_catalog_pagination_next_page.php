<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestProductList;
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

$request = RequestProductList::createFromCategory(CategoryEnum::FREE_GAMES);
$result = [];

do {
    $currentPageNumber = $request->pageArgs->offset ? (int)($request->pageArgs->offset / $request->pageArgs->size) + 1 : 1;
    $currentPageResult = $client->getProductList($request);
    
    // $currentPageResult is now a CatalogResponseDataCategoryGridRetrieve DTO object
    $result['Result for page number - ' . $currentPageNumber] = $currentPageResult;
    
    if ($currentPageResult->pageInfo && $currentPageResult->pageInfo->totalCount !== null) {
        $totalCount = $currentPageResult->pageInfo->totalCount;
        $request = $request->createNextPageRequest();
    } else {
        break;
    }
} while ($request->pageArgs->offset < $totalCount);

echo json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
