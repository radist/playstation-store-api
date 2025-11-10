<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestProductById;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

/**
 * Example for https://store.playstation.com/en-us/product/UP0001-CUSA09311_00-GAME000000000000
 */
$request = new RequestProductById('UP0001-CUSA09311_00-GAME000000000000');
$product = $client->getProductById($request);

// $product is now a Product DTO object
echo json_encode($product, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
