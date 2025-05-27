<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestPSPlusTier;
use PlaystationStoreApi\Enum\PSPlusTierEnum;

class RequestPSPlusTierTest extends TestCase
{
    public function testConstructor(): void
    {
        $tier = PSPlusTierEnum::ESSENTIAL;
        $req = new RequestPSPlusTier($tier);
        $this->assertSame($tier, $req->tierLabel);
    }
} 