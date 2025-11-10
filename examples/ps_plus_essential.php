<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\PSPlusTierEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestPSPlusTier;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

$request = new RequestPSPlusTier(PSPlusTierEnum::ESSENTIAL);
$offers = $client->getPSPlusTier($request);

// $offers is now a PSPlusOffersResponseDataTierSelectorOffersRetrieve DTO object
echo json_encode($offers, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
