<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Request\RequestCatalog;
use PlaystationStoreApi\ValueObject\Pagination;
use PlaystationStoreApi\ValueObject\Sorting;

class RequestCatalogTest extends TestCase
{
    public function testCreateFromCategory(): void
    {
        $request = RequestCatalog::createFromCategory(CategoryEnum::PS5_GAMES);

        $this->assertSame(CategoryEnum::PS5_GAMES->value, $request->id);
        $this->assertInstanceOf(Pagination::class, $request->pageArgs);
        $this->assertEquals(RequestCatalog::DEFAULT_PAGINATION_SIZE, $request->pageArgs->size);
        $this->assertInstanceOf(Sorting::class, $request->sortBy);
    }

    public function testCreateFromCategoryWithCustomPagination(): void
    {
        $pagination = new Pagination(50, 100);
        $request = RequestCatalog::createFromCategory(CategoryEnum::PS5_GAMES, $pagination);

        $this->assertSame($pagination, $request->pageArgs);
    }

    public function testCreateNextPageRequest(): void
    {
        $request = RequestCatalog::createFromCategory(CategoryEnum::PS5_GAMES);
        $nextPageRequest = $request->createNextPageRequest();

        $this->assertNotSame($request, $nextPageRequest);
        $this->assertSame($request->id, $nextPageRequest->id);
        $this->assertSame($request->pageArgs->size, $nextPageRequest->pageArgs->size);
        $this->assertEquals(
            $request->pageArgs->size,
            $nextPageRequest->pageArgs->offset
        );
    }

    public function testFilterByAndFacetOptions(): void
    {
        $request = RequestCatalog::createFromCategory(CategoryEnum::PS5_GAMES);

        $request->filterBy['test'] = 'value';
        $request->facetOptions['option'] = 'value';

        $this->assertEquals(['test' => 'value'], $request->filterBy);
        $this->assertEquals(['option' => 'value'], $request->facetOptions);
    }
}
