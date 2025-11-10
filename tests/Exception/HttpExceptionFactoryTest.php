<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Exception;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Exception\HttpExceptionFactory;
use PlaystationStoreApi\Exception\PsnApiBadRequestException;
use PlaystationStoreApi\Exception\PsnApiForbiddenException;
use PlaystationStoreApi\Exception\PsnApiNotFoundException;
use PlaystationStoreApi\Exception\PsnApiServerException;

/**
 * Test for HttpExceptionFactory
 */
final class HttpExceptionFactoryTest extends TestCase
{
    public function testCreateBadRequestException(): void
    {
        $exception = HttpExceptionFactory::create(400, 'Bad Request', ['error' => 'test']);

        $this->assertInstanceOf(PsnApiBadRequestException::class, $exception);
        $this->assertSame('Bad Request', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame(400, $exception->httpStatusCode);
        $this->assertSame(['error' => 'test'], $exception->responseData);
    }

    public function testCreateForbiddenException(): void
    {
        $exception = HttpExceptionFactory::create(403, 'Forbidden');

        $this->assertInstanceOf(PsnApiForbiddenException::class, $exception);
        $this->assertSame('Forbidden', $exception->getMessage());
        $this->assertSame(403, $exception->getCode());
        $this->assertSame(403, $exception->httpStatusCode);
        $this->assertNull($exception->responseData);
    }

    public function testCreateNotFoundException(): void
    {
        $exception = HttpExceptionFactory::create(404, 'Not Found');

        $this->assertInstanceOf(PsnApiNotFoundException::class, $exception);
        $this->assertSame('Not Found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame(404, $exception->httpStatusCode);
    }

    public function testCreateServerException(): void
    {
        $exception = HttpExceptionFactory::create(500, 'Internal Server Error');

        $this->assertInstanceOf(PsnApiServerException::class, $exception);
        $this->assertSame('Internal Server Error', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertSame(500, $exception->httpStatusCode);
    }

    public function testCreateServerExceptionWithCustomStatusCode(): void
    {
        $exception = HttpExceptionFactory::create(502, 'Bad Gateway');

        $this->assertInstanceOf(PsnApiServerException::class, $exception);
        $this->assertSame('Bad Gateway', $exception->getMessage());
        $this->assertSame(502, $exception->getCode());
        $this->assertSame(502, $exception->httpStatusCode);
    }

    public function testCreateClientError(): void
    {
        $exception = HttpExceptionFactory::createClientError(422, 'Unprocessable Entity');

        $this->assertInstanceOf(PsnApiServerException::class, $exception);
        $this->assertSame('Unprocessable Entity', $exception->getMessage());
        $this->assertSame(422, $exception->getCode());
        $this->assertSame(422, $exception->httpStatusCode);
    }

    public function testCreateClientErrorWithInvalidStatusCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status code must be in 4xx range for client errors');

        HttpExceptionFactory::createClientError(500, 'Server Error');
    }

    public function testCreateServerError(): void
    {
        $exception = HttpExceptionFactory::createServerError(503, 'Service Unavailable');

        $this->assertInstanceOf(PsnApiServerException::class, $exception);
        $this->assertSame('Service Unavailable', $exception->getMessage());
        $this->assertSame(503, $exception->getCode());
        $this->assertSame(503, $exception->httpStatusCode);
    }

    public function testCreateServerErrorWithInvalidStatusCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status code must be in 5xx range for server errors');

        HttpExceptionFactory::createServerError(400, 'Bad Request');
    }

    public function testCreateWithDefaultMessage(): void
    {
        $exception = HttpExceptionFactory::create(400);

        $this->assertInstanceOf(PsnApiBadRequestException::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
    }
}
