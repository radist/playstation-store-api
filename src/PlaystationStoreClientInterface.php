<?php

declare(strict_types=1);

namespace PlaystationStoreApi;

use PlaystationStoreApi\Dto\AddOns\AddOnsResponseDataAddOnProductsByTitleIdRetrieve;
use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;
use PlaystationStoreApi\Dto\Subscription\PSPlusOffersResponseDataTierSelectorOffersRetrieve;
use PlaystationStoreApi\Exception\PsnApiException;
use PlaystationStoreApi\Request\BaseRequest;
use PlaystationStoreApi\Request\RequestAddOnsByTitleId;
use PlaystationStoreApi\Request\RequestCatalog;
use PlaystationStoreApi\Request\RequestConceptById;
use PlaystationStoreApi\Request\RequestConceptByProductId;
use PlaystationStoreApi\Request\RequestConceptStarRating;
use PlaystationStoreApi\Request\RequestPricingDataByConceptId;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductStarRating;
use PlaystationStoreApi\Request\RequestPSPlusTier;

/**
 * Interface for PlayStation Store API client
 *
 * This interface allows users to mock the client in their unit tests
 * without needing to make real HTTP requests or mock low-level PSR-18 clients.
 */
interface PlaystationStoreClientInterface
{
    /**
     * Override SHA-256 hash for a specific operation
     *
     * @param string $operationName Operation name (e.g., 'metGetProductById')
     * @param string $newHash New SHA-256 hash
     */
    public function overrideSha256Hash(string $operationName, string $newHash): void;

    /**
     * Execute a request and return the denormalized DTO
     *
     * @throws PsnApiException
     */
    public function execute(BaseRequest $request): object;

    /**
     * Get product by ID
     *
     * @throws PsnApiException
     */
    public function getProductById(RequestProductById $request): Product;

    /**
     * Get catalog (list of concepts)
     *
     * Note: API returns concepts, not products directly.
     * Each concept contains a products array with related products.
     * Use CatalogResponseDataCategoryGridRetrieve::getAllProducts() to extract all products from concepts.
     *
     * @throws PsnApiException
     */
    public function getCatalog(RequestCatalog $request): CatalogResponseDataCategoryGridRetrieve;

    /**
     * Get concept by ID
     *
     * @throws PsnApiException
     */
    public function getConceptById(RequestConceptById $request): Concept;

    /**
     * Get add-ons by title ID
     *
     * @throws PsnApiException
     */
    public function getAddOnsByTitleId(RequestAddOnsByTitleId $request): AddOnsResponseDataAddOnProductsByTitleIdRetrieve;

    /**
     * Get PS Plus tier offers
     *
     * @throws PsnApiException
     */
    public function getPSPlusTier(RequestPSPlusTier $request): PSPlusOffersResponseDataTierSelectorOffersRetrieve;

    /**
     * Get concept by product ID
     *
     * @throws PsnApiException
     */
    public function getConceptByProductId(RequestConceptByProductId $request): Concept;

    /**
     * Get concept star rating
     *
     * @throws PsnApiException
     */
    public function getConceptStarRating(RequestConceptStarRating $request): Concept;

    /**
     * Get pricing data by concept ID
     *
     * @throws PsnApiException
     */
    public function getPricingDataByConceptId(RequestPricingDataByConceptId $request): Concept;

    /**
     * Get product star rating
     *
     * @throws PsnApiException
     */
    public function getProductStarRating(RequestProductStarRating $request): Product;
}
