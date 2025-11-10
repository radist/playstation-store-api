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
}
