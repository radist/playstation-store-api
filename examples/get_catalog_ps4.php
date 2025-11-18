<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestCatalog;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

$request = RequestCatalog::createFromCategory(CategoryEnum::PS4_GAMES);
$catalog = $client->getCatalog($request);

// $catalog is now a CatalogResponseDataCategoryGridRetrieve DTO object
echo json_encode($catalog, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
