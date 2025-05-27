<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestConceptById;

class RequestConceptByIdTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestConceptById('concept-123');
        $this->assertSame('concept-123', $req->conceptId);
    }
} 