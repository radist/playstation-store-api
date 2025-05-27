<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestProductById;

class RequestProductByIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestProductById('product-111');
        $this->assertSame('product-111', $req->productId);
    }
} 