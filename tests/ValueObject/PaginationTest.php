<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\ValueObject\Pagination;

class PaginationTest extends TestCase
{
    public function testConstructor(): void
    {
        $pagination = new Pagination(20, 40);

        $this->assertSame(20, $pagination->size);
        $this->assertSame(40, $pagination->offset);
    }

    public function testConstructorWithDefaultOffset(): void
    {
        $pagination = new Pagination(20);

        $this->assertSame(20, $pagination->size);
        $this->assertSame(0, $pagination->offset);
    }

    public function testConstructorThrowsExceptionForNegativeSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Size must be greater than 0, got: -1');

        new Pagination(-1);
    }

    public function testConstructorThrowsExceptionForZeroSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Size must be greater than 0, got: 0');

        new Pagination(0);
    }

    public function testConstructorThrowsExceptionForNegativeOffset(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Offset must be greater than or equal to 0, got: -1');

        new Pagination(20, -1);
    }
}
