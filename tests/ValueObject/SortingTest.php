<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Enum\CatalogSortingEnum;
use PlaystationStoreApi\Enum\SortingDirectionEnum;
use PlaystationStoreApi\ValueObject\Sorting;

class SortingTest extends TestCase
{
    public function testCreateFromCatalogSorting(): void
    {
        $sorting = Sorting::createFromCatalogSorting(CatalogSortingEnum::RELEASE_DATE);

        $this->assertSame(CatalogSortingEnum::RELEASE_DATE->value, $sorting->name);
        $this->assertFalse($sorting->isAscending);
    }

    public function testCreateFromCatalogSortingWithDirection(): void
    {
        $sorting = Sorting::createFromCatalogSorting(
            CatalogSortingEnum::RELEASE_DATE,
            SortingDirectionEnum::ASC
        );

        $this->assertSame(CatalogSortingEnum::RELEASE_DATE->value, $sorting->name);
        $this->assertTrue($sorting->isAscending);
    }

    public function testConstructor(): void
    {
        $sorting = new Sorting('test', SortingDirectionEnum::ASC);

        $this->assertSame('test', $sorting->name);
        $this->assertTrue($sorting->isAscending);
    }
}
