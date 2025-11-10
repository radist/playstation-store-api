<?php

declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestConceptStarRating;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client with factory (auto-detects available HTTP client implementations)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

/**
 * Example for https://store.playstation.com/en-us/concept/10002694
 */
$request = new RequestConceptStarRating('10002694');
$concept = $client->getConceptStarRating($request);

// $concept is now a Concept DTO object
echo json_encode($concept, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
