<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestProductStarRating;

class RequestProductStarRatingTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestProductStarRating('product-222');
        $this->assertSame('product-222', $req->productId);
    }
}
