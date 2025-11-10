<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Enum\CatalogSortingEnum;
use PlaystationStoreApi\Enum\CategoryEnum;
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\Enum\RegionEnum;

class EnumFromNameTest extends TestCase
{
    public function testCategoryEnumFromName()
    {
        $enum = CategoryEnum::valueFromName('NEW_GAMES');
        $this->assertInstanceOf(CategoryEnum::class, $enum);
        $this->assertSame('NEW_GAMES', $enum->name);
        $this->assertSame('e1699f77-77e1-43ca-a296-26d08abacb0f', $enum->value);
    }

    public function testRegionEnumFromName()
    {
        $enum = RegionEnum::valueFromName('TURKEY_ENGLISH');
        $this->assertInstanceOf(RegionEnum::class, $enum);
        $this->assertSame('TURKEY_ENGLISH', $enum->name);
        $this->assertSame('en-tr', $enum->value);
    }

    public function testCatalogSortingEnumFromName()
    {
        $enum = CatalogSortingEnum::valueFromName('RELEASE_DATE');
        $this->assertInstanceOf(CatalogSortingEnum::class, $enum);
        $this->assertSame('RELEASE_DATE', $enum->name);
        $this->assertSame('productReleaseDate', $enum->value);
    }

    public function testOperationSha256EnumFromName()
    {
        $enum = OperationSha256Enum::valueFromName('metGetProductById');
        $this->assertInstanceOf(OperationSha256Enum::class, $enum);
        $this->assertSame('metGetProductById', $enum->name);
        $this->assertSame('a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a', $enum->value);
    }

    public function testPSPlusTierEnumFromName()
    {
        $enum = \PlaystationStoreApi\Enum\PSPlusTierEnum::valueFromName('ESSENTIAL');
        $this->assertInstanceOf(\PlaystationStoreApi\Enum\PSPlusTierEnum::class, $enum);
        $this->assertSame('ESSENTIAL', $enum->name);
        $this->assertSame('TIER_10', $enum->value);
    }

    public function testSortingDirectionEnumFromName()
    {
        $enum = \PlaystationStoreApi\Enum\SortingDirectionEnum::valueFromName('ASC');
        $this->assertInstanceOf(\PlaystationStoreApi\Enum\SortingDirectionEnum::class, $enum);
        $this->assertSame('ASC', $enum->name);
        $this->assertSame(1, $enum->value);
    }

    public function testThrowsOnInvalidName()
    {
        $this->expectException(Error::class);
        CategoryEnum::valueFromName('NOT_EXIST');
    }
}
