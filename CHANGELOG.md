## 3.0.2 - 2025-01-XX

**BREAKING CHANGES:** This version includes breaking changes. See migration notes below.

### Changed
- **BREAKING**: Renamed `RequestProductList` to `RequestCatalog` - API returns concepts, not products directly
- **BREAKING**: Renamed `Client::getProductList()` to `Client::getCatalog()` - more accurate naming
- **BREAKING**: `CatalogResponseDataCategoryGridRetrieve::products` is usually empty - products are nested inside concepts
- Updated all examples and tests to use new naming

### Added
- **📦 Enhanced Concept DTO**: Expanded `Concept` DTO to match full API response structure:
  - Added `media` array property with `Media[]` type
  - Added `personalizedMeta` property with `PersonalizedMeta` DTO
  - Added `price` property with enhanced `Price` DTO (SkuPrice)
  - Added `products` array property with `Product[]` type
  - Added `__typename` property
- **💰 Enhanced Price DTO**: Extended `Price` DTO to support SkuPrice structure with additional fields:
  - Added `__typename`, `discountText`, `includesBundleOffer`, `isExclusive`, `isTiedToSubscription`
  - Added `serviceBranding` and `upsellServiceBranding` arrays
  - Added `upsellText` property
  - All new fields are nullable for backward compatibility
- **🎯 PersonalizedMeta DTO**: Created new `PersonalizedMeta` DTO for concept personalized metadata with `hasMediaOverrides` flag and `media` array

## 3.0.1 - 2025-11-10

### Fixed
- **🐛 Media DTO Recursive Structure**: Fixed `Media` DTO to support recursive structure with nested media objects (e.g., video frames). Previously, `$product->media` was incorrectly deserialized as an array of arrays instead of an array of `Media` objects. This fix ensures strict typing and prevents runtime errors when accessing media properties (e.g., `$media->url`). **Note:** This is a bug fix but may be breaking for code that was adapted to work with the incorrect structure.
- **🔧 PropertyInfoExtractor Integration**: Configured `ObjectNormalizer` with `PropertyInfoExtractor` (using `PhpDocExtractor` and `ReflectionExtractor`) to automatically read PHPDoc annotations (`/** @var Media[]|null */`) and correctly deserialize nested arrays of objects. This ensures that all `Media[]` properties (at any nesting level) are properly deserialized as arrays of `Media` objects, not arrays of arrays.

## 3.0.0 - 2025-11-10

**BREAKING CHANGES:** This is a major version update with breaking changes. See [Migration Guide](MIGRATION.md) for details.

### Added
- **🎯 Strictly Typed Responses**: All API responses return strongly typed DTO objects directly (e.g., `Product`, `Concept`)
- **🔧 PSR-18/PSR-17 Compatible**: Full support for PSR HTTP standards (requires PSR-18 HTTP Client and PSR-17 HTTP Factory implementations)
- **⚡ Symfony Serializer Integration**: Automatic JSON deserialization to DTOs using Symfony Serializer
- **🛡️ Enhanced Error Handling**: Typed exceptions for different HTTP status codes
- **📚 PHP 8.2+ Features**: Readonly properties, match expressions, and modern PHP syntax
- **🎯 Custom Response Denormalizer**: Created `PlaystationResponseDenormalizer` that automatically unwraps API responses, eliminating the need for wrapper DTOs
- **🔧 Hash Override Support**: Added `overrideSha256Hash(string $operationName, string $newHash)` method to `Client` class for runtime hash replacement
- **⚡ Auto-Dependency Detection**: `ClientFactory` automatically detects and uses installed PSR-18/PSR-17 implementations (Guzzle, Nyholm PSR7, Symfony HTTP Client) when dependencies are not explicitly provided
- Added `suggest` section in `composer.json` with recommendations for HTTP client and request factory implementations
- Added `getOperationName()`, `getSha256Hash()`, and `getDataPath()` methods to `BaseRequest` interface
- Added universal `execute(BaseRequest $request): object` method to `Client` class for executing any request
- Added `getConceptByProductId()`, `getConceptStarRating()`, `getPricingDataByConceptId()`, and `getProductStarRating()` methods to `Client` class
- Enhanced type hints for collections in DTOs using PHPDoc annotations (`Media[]`, `Product[]`, `Concept[]`, etc.)
- Added comprehensive unit tests with 80%+ code coverage

### Changed
- **BREAKING**: PHP version requirement: 8.2+
- **BREAKING**: All client methods return strongly typed DTO objects:
  - `getProductById()` returns `Product`
  - `getConceptById()` returns `Concept`
  - `getCatalog()` returns `CatalogResponseDataCategoryGridRetrieve` (renamed from `getProductList()`)
  - `getAddOnsByTitleId()` returns `AddOnsResponseDataAddOnProductsByTitleIdRetrieve`
  - `getPSPlusTier()` returns `PSPlusOffersResponseDataTierSelectorOffersRetrieve`
- **BREAKING**: `Client` constructor requires PSR-18/PSR-17 interfaces and Symfony Serializer
- **BREAKING**: `ClientFactory::create()` accepts optional HTTP client and request factory parameters (auto-detected if not provided and dependencies are installed)
- **BREAKING**: Request classes implement `getOperationName()` and `getSha256Hash()` methods using `OperationSha256Enum`
- **BREAKING**: All `Request` classes return target DTO classes in `getResponseDtoClass()`:
  - `RequestProductById` → `Product::class`
  - `RequestConceptById` → `Concept::class`
  - `RequestCatalog` → `CatalogResponseDataCategoryGridRetrieve::class` (renamed from `RequestProductList`)
  - `RequestAddOnsByTitleId` → `AddOnsResponseDataAddOnProductsByTitleIdRetrieve::class`
  - `RequestPSPlusTier` → `PSPlusOffersResponseDataTierSelectorOffersRetrieve::class`
- Request classes contain their own metadata (operationName and SHA-256 hash)
- All public client methods are proxies to `execute()` method
- DTO collections use PHPDoc type annotations (`Media[]`, `Product[]`, `Concept[]`, etc.)

### Removed
- **BREAKING**: No wrapper DTO classes (`ProductResponse`, `ConceptResponse`, `CatalogResponse`, `AddOnsResponse`, `PSPlusOffersResponse` and their `*ResponseData` variants) - responses are deserialized directly to target DTOs
- **BREAKING**: No `RequestLocatorService` class - metadata is embedded in request classes
- **BREAKING**: No direct Guzzle HTTP client dependency - uses PSR-18 interface

## 2.1.0 - 2024-01-11

- Added new method for get star rating information
- Added category id with all concepts
- Update parameters for RequestProductList

## 2.0.1 - 2023-06-18

- Added new method for create next page request
- Added examples

## 2.0.0 - 2023-04-30

- Switching to a new API client
- Refusal of rest-api
- Added examples
- Added make commands

## 0.3 - 2022-12-14

- changed the logic of region and language parsing
- fix support php7.4
- Added docker-compose.yaml
- Added examples
