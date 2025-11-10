<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Client;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Exception\PsnApiException;
use PlaystationStoreApi\Request\RequestConceptByProductId;
use PlaystationStoreApi\Request\RequestConceptStarRating;
use PlaystationStoreApi\Request\RequestPricingDataByConceptId;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductStarRating;
use PlaystationStoreApi\Serializer\PlaystationResponseDenormalizer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Test for Client
 */
final class ClientTest extends TestCase
{
    private Client $client;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private SerializerInterface $serializer;
    private ResponseInterface $response;
    private StreamInterface $stream;
    private RequestInterface $request;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);

        // Configure PropertyInfoExtractor to read PHPDoc annotations
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor], // PropertyListExtractorInterface
            [$phpDocExtractor, $reflectionExtractor], // PropertyTypeExtractorInterface (PhpDocExtractor has priority)
            [$phpDocExtractor], // PropertyDescriptionExtractorInterface
            [$reflectionExtractor], // PropertyAccessExtractorInterface
            [$reflectionExtractor] // PropertyInitializableExtractorInterface
        );

        $objectNormalizer = new ObjectNormalizer(
            null,
            null,
            new PropertyAccessor(),
            $propertyInfo
        );
        $this->serializer = new Serializer([
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new PlaystationResponseDenormalizer($objectNormalizer),
            $objectNormalizer,
        ], [new JsonEncoder()]);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->stream = $this->createMock(StreamInterface::class);
        $this->request = $this->createMock(RequestInterface::class);

        $this->client = new Client(
            RegionEnum::UNITED_STATES,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer
        );
    }

    public function testGetProductByIdSuccess(): void
    {
        $request = new RequestProductById('CUSA12345_00');
        $responseData = [
            'data' => [
                'productRetrieve' => [
                    'id' => 'CUSA12345_00',
                    'name' => 'Test Game',
                    'invariantName' => 'test-game',
                    'platforms' => ['PS4', 'PS5'],
                    'publisherName' => 'Test Publisher',
                    'releaseDate' => '2023-01-01T00:00:00Z',
                    'storeDisplayClassification' => 'FULL_GAME',
                    'price' => [
                        'basePrice' => '59.99',
                        'discountedPrice' => '39.99',
                        'isFree' => false,
                        'currencyCode' => 'USD',
                    ],
                    'media' => [],
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $result = $this->client->getProductById($request);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame('CUSA12345_00', $result->id);
        $this->assertSame('Test Game', $result->name);
    }

    public function testGetProductByIdWithApiErrors(): void
    {
        $request = new RequestProductById('INVALID_ID');
        $responseData = [
            'data' => null,
            'errors' => [
                [
                    'message' => 'Product not found',
                    'path' => ['productRetrieve'],
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Product not found');

        $this->client->getProductById($request);
    }

    public function testGetProductByIdWithHttpError(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        $this->setupHttpError(404, 'Not Found');

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Not Found');

        $this->client->getProductById($request);
    }

    public function testGetProductByIdWithServerError(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        $this->setupHttpError(500, 'Internal Server Error');

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Internal Server Error');

        $this->client->getProductById($request);
    }

    public function testGetConceptByProductIdSuccess(): void
    {
        $request = new RequestConceptByProductId('CUSA12345_00');
        $responseData = [
            'data' => [
                'conceptRetrieve' => [
                    'id' => '10002694',
                    'name' => 'Test Concept',
                    'invariantName' => 'test-concept',
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $result = $this->client->getConceptByProductId($request);

        $this->assertInstanceOf(Concept::class, $result);
        $this->assertSame('10002694', $result->id);
        $this->assertSame('Test Concept', $result->name);
    }

    public function testGetConceptStarRatingSuccess(): void
    {
        $request = new RequestConceptStarRating('10002694');
        $responseData = [
            'data' => [
                'conceptRetrieve' => [
                    'id' => '10002694',
                    'name' => 'Test Concept',
                    'invariantName' => 'test-concept',
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $result = $this->client->getConceptStarRating($request);

        $this->assertInstanceOf(Concept::class, $result);
        $this->assertSame('10002694', $result->id);
    }

    public function testGetPricingDataByConceptIdSuccess(): void
    {
        $request = new RequestPricingDataByConceptId('10002694');
        $responseData = [
            'data' => [
                'conceptRetrieve' => [
                    'id' => '10002694',
                    'name' => 'Test Concept',
                    'invariantName' => 'test-concept',
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $result = $this->client->getPricingDataByConceptId($request);

        $this->assertInstanceOf(Concept::class, $result);
        $this->assertSame('10002694', $result->id);
    }

    public function testGetProductStarRatingSuccess(): void
    {
        $request = new RequestProductStarRating('CUSA12345_00');
        $responseData = [
            'data' => [
                'productRetrieve' => [
                    'id' => 'CUSA12345_00',
                    'name' => 'Test Game',
                    'invariantName' => 'test-game',
                    'platforms' => ['PS4'],
                    'media' => [],
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        $result = $this->client->getProductStarRating($request);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame('CUSA12345_00', $result->id);
        $this->assertSame('Test Game', $result->name);
    }

    public function testOverrideSha256Hash(): void
    {
        $request = new RequestProductById('CUSA12345_00');
        $newHash = 'new_hash_value_for_testing';
        $responseData = [
            'data' => [
                'productRetrieve' => [
                    'id' => 'CUSA12345_00',
                    'name' => 'Test Game',
                    'invariantName' => 'test-game',
                    'platforms' => ['PS4'],
                    'media' => [],
                ],
            ],
        ];

        // Override hash
        $this->client->overrideSha256Hash('metGetProductById', $newHash);

        // Setup request to capture the URI
        $capturedUri = null;
        $this->requestFactory->method('createRequest')
            ->willReturnCallback(function ($method, $uri) use (&$capturedUri) {
                $capturedUri = $uri;

                return $this->request;
            });

        $this->setupSuccessfulRequest($responseData);

        $this->client->getProductById($request);

        // Verify that the overridden hash is used in the URI
        $this->assertStringContainsString($newHash, $capturedUri);
        $this->assertStringNotContainsString('a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a', $capturedUri);
    }

    private function setupSuccessfulRequest(array $responseData): void
    {
        $this->stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $this->response->method('getBody')
            ->willReturn($this->stream);

        $this->response->method('getStatusCode')
            ->willReturn(200);

        $this->requestFactory->method('createRequest')
            ->willReturn($this->request);

        $this->request->method('withHeader')
            ->willReturnSelf();

        $this->httpClient->method('sendRequest')
            ->willReturn($this->response);
    }

    private function setupHttpError(int $statusCode, string $reasonPhrase): void
    {
        $this->stream->method('getContents')
            ->willReturn(json_encode(['message' => $reasonPhrase]));

        $this->response->method('getBody')
            ->willReturn($this->stream);

        $this->response->method('getStatusCode')
            ->willReturn($statusCode);

        $this->response->method('getReasonPhrase')
            ->willReturn($reasonPhrase);

        $this->requestFactory->method('createRequest')
            ->willReturn($this->request);

        $this->request->method('withHeader')
            ->willReturnSelf();

        $this->httpClient->method('sendRequest')
            ->willReturn($this->response);
    }
}
