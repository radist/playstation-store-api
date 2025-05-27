<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestPricingDataByConceptId;

class RequestPricingDataByConceptIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestPricingDataByConceptId('concept-321');
        $this->assertSame('concept-321', $req->conceptId);
    }
}
