<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Tests;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Response;
use PlaystationStoreApi\Client;
use PlaystationStoreApi\Enum\OperationSha256Enum;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\RequestLocatorService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ClientTest extends TestCase
{
    private Client $client;
    private ClientInterface $httpClient;
    private RequestLocatorService $requestLocator;

    protected function setUp(): void
    {
        $this->httpClient = $this->createStub(ClientInterface::class);
        $this->requestLocator = RequestLocatorService::default();
        $this->client = new Client(RegionEnum::UNITED_STATES, $this->httpClient, $this->requestLocator);
    }

    public function testGetResponse(): void
    {
        $request = new RequestProductById('test-id');
        $expectedResponse = new Response(200, [], '{"data": {"test": "value"}}');
        
        $this->httpClient->method('request')
            ->willReturn($expectedResponse);

        $response = $this->client->getResponse($request);
        $this->assertSame($expectedResponse, $response);
    }

    public function testGet(): void
    {
        $request = new RequestProductById('test-id');
        $expectedData = ['data' => ['test' => 'value']];
        $response = new Response(200, [], json_encode($expectedData, JSON_THROW_ON_ERROR));

        $this->httpClient->method('request')
            ->willReturn($response);

        $result = $this->client->get($request);
        $this->assertEquals($expectedData, $result);
    }

    public function testGetWithCookieRetry(): void
    {
        $request = new RequestProductById('test-id');
        $cookieResponse = new Response(200, ['Set-Cookie' => ['test=value']]);
        $finalResponse = new Response(200, [], '{"data": {"test": "value"}}');

        $this->httpClient->method('request')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new \GuzzleHttp\Exception\BadResponseException(
                    'Error',
                    $this->createStub(RequestInterface::class),
                    $cookieResponse
                )),
                $finalResponse
            );

        $result = $this->client->get($request);
        $this->assertEquals(['data' => ['test' => 'value']], $result);
    }
} 