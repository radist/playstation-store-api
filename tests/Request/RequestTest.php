<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\AddOns\AddOnsResponseDataAddOnProductsByTitleIdRetrieve;
use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;
use PlaystationStoreApi\Dto\Subscription\PSPlusOffersResponseDataTierSelectorOffersRetrieve;
use PlaystationStoreApi\Enum\PSPlusTierEnum;
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
 * Test for Request classes
 */
final class RequestTest extends TestCase
{
    public function testRequestProductByIdGetResponseDtoClass(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        $this->assertSame(Product::class, $request->getResponseDtoClass());
    }

    public function testRequestCatalogGetResponseDtoClass(): void
    {
        $request = new RequestCatalog('category-id', new \PlaystationStoreApi\ValueObject\Pagination(20), new \PlaystationStoreApi\ValueObject\Sorting('name'));

        $this->assertSame(CatalogResponseDataCategoryGridRetrieve::class, $request->getResponseDtoClass());
    }

    public function testRequestConceptByIdGetResponseDtoClass(): void
    {
        $request = new RequestConceptById('concept-id');

        $this->assertSame(Concept::class, $request->getResponseDtoClass());
    }

    public function testRequestConceptByProductIdGetResponseDtoClass(): void
    {
        $request = new RequestConceptByProductId('product-id');

        $this->assertSame(Concept::class, $request->getResponseDtoClass());
    }

    public function testRequestConceptStarRatingGetResponseDtoClass(): void
    {
        $request = new RequestConceptStarRating('concept-id');

        $this->assertSame(Concept::class, $request->getResponseDtoClass());
    }

    public function testRequestPricingDataByConceptIdGetResponseDtoClass(): void
    {
        $request = new RequestPricingDataByConceptId('concept-id');

        $this->assertSame(Concept::class, $request->getResponseDtoClass());
    }

    public function testRequestProductStarRatingGetResponseDtoClass(): void
    {
        $request = new RequestProductStarRating('product-id');

        $this->assertSame(Product::class, $request->getResponseDtoClass());
    }

    public function testRequestAddOnsByTitleIdGetResponseDtoClass(): void
    {
        $request = new RequestAddOnsByTitleId('title-id');

        $this->assertSame(AddOnsResponseDataAddOnProductsByTitleIdRetrieve::class, $request->getResponseDtoClass());
    }

    public function testRequestPSPlusTierGetResponseDtoClass(): void
    {
        $request = new RequestPSPlusTier(PSPlusTierEnum::ESSENTIAL);

        $this->assertSame(PSPlusOffersResponseDataTierSelectorOffersRetrieve::class, $request->getResponseDtoClass());
    }

    public function testRequestProductByIdGetOperationName(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        $this->assertSame('metGetProductById', $request->getOperationName());
    }

    public function testRequestProductByIdGetSha256Hash(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        $this->assertSame('a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a', $request->getSha256Hash());
    }

    public function testRequestConceptByIdGetOperationName(): void
    {
        $request = new RequestConceptById('concept-id');

        $this->assertSame('metGetConceptById', $request->getOperationName());
    }

    public function testRequestConceptByIdGetSha256Hash(): void
    {
        $request = new RequestConceptById('concept-id');

        $this->assertSame('cc90404ac049d935afbd9968aef523da2b6723abfb9d586e5f77ebf7c5289006', $request->getSha256Hash());
    }

    public function testRequestCatalogGetOperationName(): void
    {
        $request = new RequestCatalog('category-id', new \PlaystationStoreApi\ValueObject\Pagination(20), new \PlaystationStoreApi\ValueObject\Sorting('name'));

        $this->assertSame('categoryGridRetrieve', $request->getOperationName());
    }

    public function testRequestCatalogGetSha256Hash(): void
    {
        $request = new RequestCatalog('category-id', new \PlaystationStoreApi\ValueObject\Pagination(20), new \PlaystationStoreApi\ValueObject\Sorting('name'));

        $this->assertSame('4ce7d410a4db2c8b635a48c1dcec375906ff63b19dadd87e073f8fd0c0481d35', $request->getSha256Hash());
    }
}
