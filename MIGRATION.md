# Migration Guide: v2.1.0 to v3.0.0

This guide will help you migrate your code from playstation-store-api v2.1.0 to v3.0.0.

## 🚨 Breaking Changes

### 1. PHP Version Requirement
- **v2.1.0**: PHP 7.4+
- **v3.0.0**: PHP 8.1+

### 2. Dependencies
- **v2.1.0**: Guzzle HTTP Client
- **v3.0.0**: PSR-18 HTTP Client + PSR-17 HTTP Factory + Symfony Serializer

### 3. Response Types
- **v2.1.0**: Returns associative arrays
- **v3.0.0**: Returns strongly typed DTO objects directly (denormalized)

### 4. Client Constructor
- **v2.1.0**: Simple constructor with Guzzle client
- **v3.0.0**: Requires PSR-18/PSR-17 interfaces and Symfony Serializer

### 5. Request Metadata
- **v2.1.0**: Metadata managed by `RequestLocatorService`
- **v3.0.0**: Metadata embedded in request classes, uses `OperationSha256Enum`

### 6. Data Access
- **v2.1.0**: Access via nested structure `$response->data->productRetrieve`
- **v3.0.0**: Direct access to denormalized DTOs `$product->name`

### 7. Hash Override
- **v3.0.0**: New `overrideSha256Hash()` method allows runtime hash replacement without code changes

## 📋 Migration Steps

### Step 1: Update Dependencies

#### v2.1.0
```json
{
    "require": {
        "php": ">=7.4",
        "guzzlehttp/guzzle": "^7.3.0"
    }
}
```

#### v3.0.0
```json
{
    "require": {
        "php": ">=8.1",
        "symfony/serializer": "^6.0",
        "symfony/property-access": "^6.0",
        "psr/http-client": "^1.0",
        "psr/http-factory": "^1.0",
        "psr/http-message": "^1.0"
    }
}
```

### Step 2: Update Client Initialization

#### v2.1.0
```php
use PlaystationStoreApi\Client;
use GuzzleHttp\Client as HTTPClient;
use PlaystationStoreApi\Enum\RegionEnum;

$client = new Client(
    RegionEnum::UNITED_STATES, 
    new HTTPClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/'])
);
```

#### v3.0.0 (Recommended - Auto-detection)
```php
use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;

// Dependencies will be auto-detected if guzzlehttp/guzzle and nyholm/psr7 are installed
$client = ClientFactory::create(RegionEnum::UNITED_STATES);
```

#### v3.0.0 (Explicit dependencies)
```php
use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;

// base_uri is optional but recommended for better performance
$httpClient = new GuzzleClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/']);
$requestFactory = new Psr17Factory();

$client = ClientFactory::create(
    RegionEnum::UNITED_STATES,
    $httpClient,
    $requestFactory
);
```

#### v3.0.0 (Manual - Custom Serializer)
```php
use PlaystationStoreApi\Client;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Serializer\PlaystationResponseDenormalizer;
use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// base_uri is optional but recommended for better performance
$httpClient = new GuzzleClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/']);
$requestFactory = new Psr17Factory();
$objectNormalizer = new ObjectNormalizer(null, null, PropertyAccess::createPropertyAccessor());
$serializer = new Serializer([
    new DateTimeNormalizer(),
    new PlaystationResponseDenormalizer($objectNormalizer),
    $objectNormalizer
], [new JsonEncoder()]);

$client = new Client(
    RegionEnum::UNITED_STATES,
    $httpClient,
    $requestFactory,
    $serializer
);
```

### Step 3: Update Method Calls

#### v2.1.0
```php
// Generic method for all requests
$response = $client->get(new RequestProductById('CUSA12345_00'));
$response = $client->get(new RequestProductList(...));
$response = $client->get(new RequestConceptById('10002694'));
```

#### v3.0.0
```php
// Specific methods for each request type (returns denormalized DTOs)
$product = $client->getProductById(new RequestProductById('CUSA12345_00'));
$catalog = $client->getProductList(new RequestProductList(...));
$concept = $client->getConceptById(new RequestConceptById('10002694'));

// New methods available in v3.0.0
$concept = $client->getConceptByProductId(new RequestConceptByProductId('product-id'));
$concept = $client->getConceptStarRating(new RequestConceptStarRating('concept-id'));
$concept = $client->getPricingDataByConceptId(new RequestPricingDataByConceptId('concept-id'));
$product = $client->getProductStarRating(new RequestProductStarRating('product-id'));

// Universal execute() method for custom requests
$result = $client->execute(new CustomRequest('id'));
```

### Step 4: Update Response Handling

#### v2.1.0 - Array Access
```php
$response = $client->get(new RequestProductById('CUSA12345_00'));

// Access data as array
$productName = $response['data']['productRetrieve']['name'];
$basePrice = $response['data']['productRetrieve']['price']['basePrice'];
$releaseDate = $response['data']['productRetrieve']['releaseDate'];

// Check for errors
if (isset($response['errors'])) {
    foreach ($response['errors'] as $error) {
        echo "Error: " . $error['message'];
    }
}
```

#### v3.0.0 - Denormalized DTO Access
```php
use PlaystationStoreApi\Dto\Product\Product;

$product = $client->getProductById(new RequestProductById('CUSA12345_00'));

// Direct access to denormalized DTO (no nested structure)
$productName = $product->name;
$basePrice = $product->price?->basePrice;
$releaseDate = $product->releaseDate;

// Type safety and IDE support
if ($product->name) {
    echo "Product: " . $product->name;
}

// Access collections with proper typing
if ($product->media) {
    foreach ($product->media as $media) {
        echo $media->url; // IDE knows $media is Media object
    }
}
```

### Step 5: Update Error Handling

#### v2.1.0
```php
try {
    $response = $client->get(new RequestProductById('invalid-id'));
} catch (ResponseException $e) {
    echo "Error: " . $e->getMessage();
}
```

#### v3.0.0
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
    
    // Access response data if available
    if ($e->responseData) {
        var_dump($e->responseData);
    }
}
```

## 🔄 Complete Migration Example

### Before (v2.1.0)
```php
<?php
declare(strict_types=1);

use PlaystationStoreApi\Client;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Enum\CategoryEnum;
use GuzzleHttp\Client as HTTPClient;

$client = new Client(
    RegionEnum::UNITED_STATES, 
    new HTTPClient(['base_uri' => 'https://web.np.playstation.com/api/graphql/v1/'])
);

// Get product
$productResponse = $client->get(new RequestProductById('CUSA12345_00'));
$productName = $productResponse['data']['productRetrieve']['name'];

// Get catalog
$catalogRequest = RequestProductList::createFromCategory(CategoryEnum::PS5_GAMES);
$catalogResponse = $client->get($catalogRequest);
$products = $catalogResponse['data']['categoryGridRetrieve']['products'];

foreach ($products as $product) {
    echo $product['name'] . "\n";
}
```

### After (v3.0.0)
```php
<?php
declare(strict_types=1);

use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Enum\CategoryEnum;

// Initialize client (dependencies auto-detected if installed)
$client = ClientFactory::create(RegionEnum::UNITED_STATES);

// Get product - returns Product DTO directly
$product = $client->getProductById(new RequestProductById('CUSA12345_00'));
$productName = $product->name;

// Get catalog - returns CatalogResponseDataCategoryGridRetrieve DTO directly
$catalogRequest = RequestProductList::createFromCategory(CategoryEnum::PS5_GAMES);
$catalog = $client->getProductList($catalogRequest);
$products = $catalog->products; // Typed as Product[]|null

if ($products) {
    foreach ($products as $product) {
        echo $product->name . "\n"; // $product is typed as Product
    }
}

// Use universal execute() method for custom requests
$customRequest = new CustomRequest('some-id');
$result = $client->execute($customRequest);
```

## 🎯 Benefits of Migration

### 1. Type Safety
- **v2.1.0**: No type checking, runtime errors possible
- **v3.0.0**: Full type safety with IDE support and static analysis
- **v3.0.0**: Enhanced collection typing (`Product[]`, `Media[]`, etc.)

### 2. Better Error Handling
- **v2.1.0**: Generic exceptions
- **v3.0.0**: Specific exceptions for different error types (`PsnApiNotFoundException`, `PsnApiServerException`, etc.)

### 3. Modern PHP Features
- **v2.1.0**: PHP 7.4 syntax
- **v3.0.0**: PHP 8.1+ features (readonly properties, match expressions, enums)

### 4. Standards Compliance
- **v2.1.0**: Guzzle-specific
- **v3.0.0**: PSR-18/PSR-17 compliant

### 5. Simplified API
- **v2.1.0**: Nested data access (`$response['data']['productRetrieve']['name']`)
- **v3.0.0**: Direct access to denormalized DTOs (`$product->name`)

### 6. Universal Request Execution
- **v2.1.0**: Limited to predefined methods
- **v3.0.0**: Universal `execute()` method for any custom request

### 7. Hash Override Support
- **v3.0.0**: Runtime hash replacement via `overrideSha256Hash()` method

### 8. Auto-Dependency Detection
- **v3.0.0**: `ClientFactory` automatically detects installed PSR-18/PSR-17 implementations

## 📚 Available Methods in v3.0.0

All methods return denormalized DTOs directly (no nested structure):

```php
// Product methods
$product = $client->getProductById(new RequestProductById('product-id'));
$product = $client->getProductStarRating(new RequestProductStarRating('product-id'));

// Concept methods
$concept = $client->getConceptById(new RequestConceptById('concept-id'));
$concept = $client->getConceptByProductId(new RequestConceptByProductId('product-id'));
$concept = $client->getConceptStarRating(new RequestConceptStarRating('concept-id'));
$concept = $client->getPricingDataByConceptId(new RequestPricingDataByConceptId('concept-id'));

// Catalog methods
$catalog = $client->getProductList(new RequestProductList(...));

// Add-ons methods
$addOns = $client->getAddOnsByTitleId(new RequestAddOnsByTitleId('title-id'));

// PS Plus methods
$offers = $client->getPSPlusTier(new RequestPSPlusTier(PSPlusTierEnum::ESSENTIAL));

// Universal method for custom requests
$result = $client->execute(new CustomRequest('id'));
```

**Important:** All methods return DTOs directly:
- `getProductById()` returns `Product` (not `ProductResponse`)
- `getConceptById()` returns `Concept` (not `ConceptResponse`)
- `getProductList()` returns `CatalogResponseDataCategoryGridRetrieve` (not `CatalogResponse`)
- etc.

## 🔧 Additional Migration Considerations

### Custom Request Classes

If you have custom request classes, you need to update them:

#### v2.1.0
```php
class CustomRequest implements BaseRequest
{
    public function getResponseDtoClass(): string
    {
        return ProductResponse::class;
    }
    
    // Metadata managed by RequestLocatorService
}
```

#### v3.0.0
```php
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\Dto\Product\Product;

class CustomRequest implements BaseRequest
{
    public function getResponseDtoClass(): string
    {
        return Product::class; // Return target DTO, not wrapper
    }
    
    public function getOperationName(): string
    {
        return OperationSha256Enum::metGetProductById->name;
        // Or return custom operation name string
    }
    
    public function getSha256Hash(): string
    {
        return OperationSha256Enum::metGetProductById->value;
        // Or return custom hash string
    }
    
    public function getDataPath(): string
    {
        return 'data.productRetrieve'; // Path to data in response
    }
}

// Usage with execute() method
$result = $client->execute(new CustomRequest('id'));
```

### RequestLocatorService Removal

If you were using `RequestLocatorService` to manage metadata:

#### v2.1.0
```php
use PlaystationStoreApi\RequestLocatorService;

$locator = RequestLocatorService::default();
$locator->set(RequestPSPlusTier::class, 'new-hash');
```

#### v3.0.0
```php
// Option 1: Override hash at runtime
$client->overrideSha256Hash('featuresRetrieve', 'new-hash');
$offers = $client->getPSPlusTier(new RequestPSPlusTier(PSPlusTierEnum::ESSENTIAL));

// Option 2: Create custom request class that overrides getSha256Hash()
class CustomPSPlusRequest extends RequestPSPlusTier
{
    public function getSha256Hash(): string
    {
        return 'new-hash';
    }
}

$request = new CustomPSPlusRequest(PSPlusTierEnum::ESSENTIAL);
$offers = $client->getPSPlusTier($request);
```

## 🚀 Quick Migration Checklist

- [ ] Update PHP to 8.1+
- [ ] Install new dependencies (PSR-18/PSR-17, Symfony Serializer, Symfony PropertyAccess)
- [ ] Replace `new Client()` with `ClientFactory::create()` (dependencies can be auto-detected)
- [ ] Update method calls from `get()` to specific methods (`getProductById`, `getConceptById`, etc.)
- [ ] **Remove nested data access**: Change `$response['data']['productRetrieve']['name']` to `$product->name`
- [ ] Update response handling from arrays to denormalized DTO properties
- [ ] Update error handling to use typed exceptions
- [ ] If using custom requests, implement `getOperationName()`, `getSha256Hash()`, and `getDataPath()` methods
- [ ] Remove any `RequestLocatorService` usage
- [ ] Update type hints in your code to use DTO classes instead of response wrappers
- [ ] Test your application thoroughly
- [ ] Update your CI/CD pipeline for PHP 8.1+

## 📞 Support

If you encounter issues during migration, please:
1. Check this migration guide
2. Review the [README.md](README.md) for updated examples
3. Open an issue on GitHub with your specific use case

## 🔗 Related Documentation

- [README.md](README.md) - Complete API documentation
- [CHANGELOG.md](CHANGELOG.md) - Detailed changelog
- [Examples](./examples/) - Working code examples
