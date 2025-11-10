<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\PSPlusTierEnum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestPSPlusTier;
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

$request = new RequestPSPlusTier(PSPlusTierEnum::DELUXE);
$offers = $client->getPSPlusTier($request);

// $offers is now a PSPlusOffersResponseDataTierSelectorOffersRetrieve DTO object
echo json_encode($offers, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
