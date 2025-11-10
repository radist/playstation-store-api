<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestConceptByProductId;

class RequestConceptByProductIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestConceptByProductId('product-456');
        $this->assertSame('product-456', $req->productId);
    }
}
