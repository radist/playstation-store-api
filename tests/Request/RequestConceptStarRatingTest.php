<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestConceptStarRating;

class RequestConceptStarRatingTest extends TestCase
{
    public function testConstructor(): void
    {
        $req = new RequestConceptStarRating('concept-789');
        $this->assertSame('concept-789', $req->conceptId);
    }
}
