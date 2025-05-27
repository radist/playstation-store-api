<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests;

use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\Exception\RequestNotFoundException;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\RequestLocatorService;
use PHPUnit\Framework\TestCase;

class RequestLocatorServiceTest extends TestCase
{
    private RequestLocatorService $locator;

    protected function setUp(): void
    {
        $this->locator = new RequestLocatorService();
    }

    public function testSetAndGet(): void
    {
        $this->locator->set(RequestProductById::class, OperationSha256Enum::metGetProductById);
        
        $result = $this->locator->get(RequestProductById::class);
        $this->assertSame(OperationSha256Enum::metGetProductById, $result);
    }

    public function testGetNotFound(): void
    {
        $this->expectException(RequestNotFoundException::class);
        $this->expectExceptionMessage('NonExistentClass not found in request locator');
        
        $this->locator->get('NonExistentClass');
    }

    public function testDefault(): void
    {
        $locator = RequestLocatorService::default();
        
        $this->assertInstanceOf(RequestLocatorService::class, $locator);
        $this->assertSame(
            OperationSha256Enum::metGetProductById,
            $locator->get(RequestProductById::class)
        );
    }
} 