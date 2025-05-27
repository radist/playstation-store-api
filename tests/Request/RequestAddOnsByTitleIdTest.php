<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests\Request;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Request\RequestAddOnsByTitleId;
use PlaystationStoreApi\ValueObject\Pagination;

class RequestAddOnsByTitleIdTest extends TestCase
{
    public function testConstructorDefaultPagination(): void
    {
        $req = new RequestAddOnsByTitleId('CUSA00001');
        $this->assertSame('CUSA00001', $req->npTitleId);
        $this->assertInstanceOf(Pagination::class, $req->pageArgs);
        $this->assertSame(RequestAddOnsByTitleId::DEFAULT_PAGINATION_SIZE, $req->pageArgs->size);
    }

    public function testConstructorCustomPagination(): void
    {
        $pagination = new Pagination(50, 10);
        $req = new RequestAddOnsByTitleId('CUSA00002', $pagination);
        $this->assertSame($pagination, $req->pageArgs);
    }
} 