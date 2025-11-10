# playstation-store-api

💖 [Support the project with a donation](https://boosty.to/tishmukhametov/donate) — it helps keep things going!

## 🚀 Version 3.0.0 - Major Update

**Breaking Changes:** This is a major version update with breaking changes. See [Migration Guide](#migration-guide) for details.

### ✨ New Features in v3.0.0

- **🎯 Strictly Typed Responses**: All API responses now return strongly typed DTO objects instead of arrays
- **🔧 PSR-18/PSR-17 Compatible**: Full support for PSR HTTP standards
- **⚡ Symfony Serializer Integration**: Automatic JSON deserialization to DTOs
- **🛡️ Enhanced Error Handling**: Typed exceptions for different HTTP status codes
- **📚 PHP 8.2+ Features**: Readonly properties, match expressions, and modern PHP syntax
- **🧪 Comprehensive Testing**: 80%+ code coverage with unit and integration tests
- **🎯 Simplified DTO Structure**: Methods now return denormalized DTOs directly (e.g., `Product` instead of `ProductResponse->data->productRetrieve`)
- **🔧 Self-Contained Requests**: Request classes now contain their own metadata (operationName and SHA-256 hash)
- **⚡ Removed Service Locator**: Eliminated `RequestLocatorService` for better code clarity and maintainability
- **📚 Improved Developer Experience**: Cleaner API with direct access to data without nested wrappers
- **🔧 Universal execute() Method**: Execute any custom request with the universal `execute()` method

## 1. Prerequisites

* PHP 8.2 or later
* PSR-18 HTTP Client implementation (e.g., Guzzle, Symfony HTTP Client)
* PSR-17 HTTP Factory implementation (e.g., Nyholm PSR7)

## 2. Installation

The playstation-store-api can be installed using Composer by running the following command:

```sh
composer require mrt1m/playstation-store-api
```

## 3. Initialization

### 3.1. Using ClientFactory (Recommended)

The `ClientFactory` can automatically detect and use installed PSR-18/PSR-17 implementations. If you have `guzzlehttp/guzzle` and `nyholm/psr7` installed, you can create a client with minimal code:

```php
<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;

require_once __DIR__ . '/../vendor/autoload.php';

// Create client - dependencies will be auto-detected if guzzlehttp/guzzle and nyholm/psr7 are installed
$client = ClientFactory::create(RegionEnum::UNITED_STATES);
```

If you need to use custom HTTP client or request factory, you can still pass them explicitly:

```php
<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;

require_once __DIR__ . '/../vendor/autoload.php';

// Create HTTP client and factory
$httpClient = new GuzzleClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/']);
$requestFactory = new Psr17Factory();

// Create client with factory
$client = ClientFactory::create(
    RegionEnum::UNITED_STATES,
    $httpClient,
    $requestFactory
);
```

### 3.2. Manual Initialization

```php
<?php
declare(strict_types=1);

use PlaystationStoreApi\Client;
use PlaystationStoreApi\Enum\RegionEnum;
use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once __DIR__ . '/../vendor/autoload.php';

// Create dependencies
$httpClient = new GuzzleClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/']);
$requestFactory = new Psr17Factory();
$serializer = new Serializer([
    new DateTimeNormalizer(),
    new ObjectNormalizer(null, null, PropertyAccess::createPropertyAccessor())
], [new JsonEncoder()]);

// Create client
$client = new Client(
    RegionEnum::UNITED_STATES,
    $httpClient,
    $requestFactory,
    $serializer
    // Optional: 'https://custom-endpoint.example.com/api/graphql/v1/' as 5th parameter for baseUri
);
```

## 4. API Requests with Typed Responses

### 4.1. Request Product by ID

```php
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Example for https://store.playstation.com/en-us/product/UP0001-CUSA09311_00-GAME000000000000
 */
$request = new RequestProductById('UP0001-CUSA09311_00-GAME000000000000');
$product = $client->getProductById($request);

// $product is now a Product DTO object
echo $product->name; // "Game Title"
echo $product->price->basePrice; // "59.99"
```

### 4.2. Request Concept by ID

```php
use PlaystationStoreApi\Request\RequestConceptById;
use PlaystationStoreApi\Dto\Concept\Concept;

/**
 * Example for https://store.playstation.com/en-us/concept/10002694
 */
$request = new RequestConceptById('10002694');
$concept = $client->getConceptById($request);

// $concept is now a Concept DTO object
echo $concept->name; // "Concept Name"
```

### 4.3. Request Catalog Data

```php
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Enum\CategoryEnum;

$request = RequestProductList::createFromCategory(CategoryEnum::PS5_GAMES);
$catalog = $client->getProductList($request);

// $catalog is now a CatalogResponseDataCategoryGridRetrieve DTO object
foreach ($catalog->products as $product) {
    echo $product['name'] . "\n";
}

// Get next page
$nextPageCatalog = $client->getProductList($request->createNextPageRequest());
```

### 4.4. Request Add-ons by Title ID

```php
use PlaystationStoreApi\Request\RequestAddOnsByTitleId;
use PlaystationStoreApi\Dto\AddOns\AddOnsResponseDataAddOnProductsByTitleIdRetrieve;

$request = new RequestAddOnsByTitleId('title-id');
$addOns = $client->getAddOnsByTitleId($request);

// $addOns is now an AddOnsResponseDataAddOnProductsByTitleIdRetrieve DTO object
foreach ($addOns->addOnProducts as $addon) {
    echo $addon['name'] . "\n";
}
```

### 4.5. Request PS Plus Offers

```php
use PlaystationStoreApi\Request\RequestPSPlusTier;
use PlaystationStoreApi\Dto\Subscription\PSPlusOffersResponseDataTierSelectorOffersRetrieve;
use PlaystationStoreApi\Enum\PSPlusTierEnum;

$request = new RequestPSPlusTier(PSPlusTierEnum::ESSENTIAL);
$offers = $client->getPSPlusTier($request);

// $offers is now a PSPlusOffersResponseDataTierSelectorOffersRetrieve DTO object
foreach ($offers->offers as $offer) {
    echo $offer['title'] . "\n";
}
```

## 5. Error Handling

### 5.1. HTTP Errors

```php
use PlaystationStoreApi\Exception\PsnApiException;
use PlaystationStoreApi\Exception\PsnApiNotFoundException;
use PlaystationStoreApi\Exception\PsnApiServerException;

try {
    $response = $client->getProductById(new RequestProductById('invalid-id'));
} catch (PsnApiNotFoundException $e) {
    echo "Product not found: " . $e->getMessage();
    echo "HTTP Status: " . $e->httpStatusCode; // 404
} catch (PsnApiServerException $e) {
    echo "Server error: " . $e->getMessage();
    echo "HTTP Status: " . $e->httpStatusCode; // 500
} catch (PsnApiException $e) {
    echo "API error: " . $e->getMessage();
}
```

### 5.2. API Errors

```php
try {
    $response = $client->getProductById(new RequestProductById('invalid-id'));
} catch (PsnApiException $e) {
    // Check if response contains API errors
    if ($e->responseData && isset($e->responseData['errors'])) {
        foreach ($e->responseData['errors'] as $error) {
            echo "API Error: " . $error['message'] . "\n";
        }
    }
}
```

## 6. Migration Guide

### 6.1. Breaking Changes from v2.x to v3.0.0

#### Response Type Changes (Denormalization)
```php
// v2.x - Returns wrapped DTO
$response = $client->getProductById(new RequestProductById('id'));
echo $response->data->productRetrieve->name;

// v3.0.0 - Returns denormalized DTO directly
$product = $client->getProductById(new RequestProductById('id'));
echo $product->name;
```

#### Request Metadata Changes
```php
// v2.x - Metadata in RequestLocatorService
$locator = RequestLocatorService::default();
$locator->set(RequestPSPlusTier::class, 'new-hash');

// v3.0.0 - Metadata in request classes
// Simply override the getSha256Hash() method in your custom request class
class CustomRequest implements BaseRequest {
    public function getSha256Hash(): string {
        return 'new-hash';
    }
}
```

### 6.2. Migration Steps

1. **Update Response Access**: Remove `.data->productRetrieve` (or similar) nesting
2. **Update Request Classes**: If using custom requests, implement `getOperationName()` and `getSha256Hash()` methods
3. **Remove RequestLocatorService**: No longer needed, metadata is in request classes
4. **Update Type Hints**: Change return types from `ProductResponse` to `Product`, etc.

## 7. Run Examples

If you want to run [examples](./examples), you need:
1) Docker and docker compose
2) Execute make command for example:
```bash
make get_add_ons_by_title_id
```
3) Get API response from [response](./response) directory

## 8. About Request Signing

For all requests you need to send sha256Hash. It's request signature.

You can get sha256Hash from browser request:
1) Open the Network panel and find query to https://web.np.playstation.com/api/graphql/v1/op
2) Copy the full request URL and use urldecode
3) sha256Hash is in the extensions parameter, example:

```
https://web.np.playstation.com/api/graphql/v1/op?operationName=categoryGridRetrieve&variables={"id":"44d8bb20-653e-431e-8ad0-c0a365f68d2f","pageArgs":{"size":24,"offset":0},"sortBy":{"name":"productReleaseDate","isAscending":false},"filterBy":[],"facetOptions":[]}&extensions={"persistedQuery":{"version":1,"sha256Hash":"9845afc0dbaab4965f6563fffc703f588c8e76792000e8610843b8d3ee9c4c09"}}
```

**⚠️ Important Warning:** SHA-256 hashes are managed by Sony and can be changed at any time without notice. This is a fundamental risk inherent in the PlayStation Store API. 

If a hash becomes invalid, you have two options:

1. **Override hash at runtime** (recommended for quick fixes):
```php
// Override hash for a specific operation
$client->overrideSha256Hash('metGetProductById', 'new_hash_value_here');
```

2. **Create a custom request class** that overrides the `getSha256Hash()` method (for permanent changes):
```php
final class CustomRequest implements BaseRequest
{
    public function getSha256Hash(): string
    {
        return 'your-custom-sha256-hash-here';
    }
    // ... other required methods
}
```

## 9. Custom Requests

If you need custom request:
1) Create new request class then implement `PlaystationStoreApi\Request\BaseRequest`
2) Implement required methods:
   - `getResponseDtoClass()`: Return expected DTO class
   - `getOperationName()`: Return operation name string
   - `getSha256Hash()`: Return SHA-256 hash string
   - `getDataPath()`: Return path to data inside DTO wrapper (e.g., `'data.productRetrieve'`)
3) Use your custom request with the universal `execute()` method

Example:
```php
use PlaystationStoreApi\Request\BaseRequest;
use PlaystationStoreApi\Dto\Product\Product;

final class CustomRequest implements BaseRequest
{
    public function __construct(public readonly string $productId)
    {
    }

    public function getResponseDtoClass(): string
    {
        return Product::class;
    }

    public function getOperationName(): string
    {
        return 'customOperation';
    }

    public function getSha256Hash(): string
    {
        return 'your-custom-sha256-hash-here';
    }

    public function getDataPath(): string
    {
        return 'data.productRetrieve';
    }
}

// Usage with execute() method
$request = new CustomRequest('product-id');
$product = $client->execute($request);
// $product is now a Product DTO object directly
```

### Using execute() Method

The `execute()` method is a universal way to execute any request that implements `BaseRequest`. It handles all the complexity of:
- Sending the HTTP request
- Error handling
- Deserialization directly to target DTO (no wrapper extraction needed)

All existing client methods (`getProductById`, `getConceptById`, etc.) are now simple proxies to `execute()` with type casting.

## 10. Postman Collection

You can try PlayStation API with [Postman](https://www.postman.com/).

For import collection download [playstation api.postman_collection.json](./postman_collection/playstation%20api.postman_collection.json)

## 11. Testing

Run tests with:
```bash
# Run all tests
docker compose run --rm php vendor/bin/phpunit

# Run tests with coverage
docker compose run --rm -e XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-text

# Run specific test suites
docker compose run --rm php vendor/bin/phpunit tests/Dto/ tests/Exception/
```

## 12. Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 13. License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.