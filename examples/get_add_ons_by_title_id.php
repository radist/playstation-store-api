<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestAddOnsByTitleId;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

/**
 * You can find value "npTitleId" param in the product or concept response
 */
$request = new RequestAddOnsByTitleId('CUSA09311_00');
$addOns = $client->getAddOnsByTitleId($request);

// $addOns is now an AddOnsResponseDataAddOnProductsByTitleIdRetrieve DTO object
echo json_encode($addOns, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
