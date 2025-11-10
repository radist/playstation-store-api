<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestConceptByProductId;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

/**
 * Example for https://store.playstation.com/en-us/product/UP0082-PPSA10664_00-FF16SIEA00000002
 */
$request = new RequestConceptByProductId('UP0082-PPSA10664_00-FF16SIEA00000002');
$concept = $client->getConceptByProductId($request);

// $concept is now a Concept DTO object
echo json_encode($concept, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
