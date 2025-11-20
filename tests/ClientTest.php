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
use PlaystationStoreApi\Serializer\EnumNormalizer;
use PlaystationStoreApi\Serializer\PlaystationResponseDenormalizer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
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
            new EnumNormalizer(),
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

    /**
     * Test logging on successful request
     */
    public function testLoggingOnSuccessfulRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $client = new Client(
            RegionEnum::UNITED_STATES,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            Client::DEFAULT_BASE_URI,
            $logger
        );

        $request = new RequestProductById('CUSA12345_00');
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

        // Capture all log calls to verify them
        $infoCalls = [];
        $debugCalls = [];

        $logger->method('info')
            ->willReturnCallback(function ($message, $context) use (&$infoCalls) {
                $infoCalls[] = ['message' => $message, 'context' => $context];
            });

        $logger->method('debug')
            ->willReturnCallback(function ($message, $context) use (&$debugCalls) {
                $debugCalls[] = ['message' => $message, 'context' => $context];
            });

        $client->getProductById($request);

        // Verify info log for sending request
        $infoFound = false;
        foreach ($infoCalls as $call) {
            if ($call['message'] === 'Sending request to PlayStation Store API') {
                $this->assertSame('metGetProductById', $call['context']['operation']);
                $this->assertSame('en-us', $call['context']['region']);
                $this->assertStringContainsString('operationName=metGetProductById', $call['context']['uri']);
                $infoFound = true;

                break;
            }
        }
        $this->assertTrue($infoFound, 'Info log for sending request was not found');

        // Verify debug log for default hash
        $debugHashFound = false;
        foreach ($debugCalls as $call) {
            if ($call['message'] === 'Using default SHA-256 hash for operation') {
                $this->assertSame('metGetProductById', $call['context']['operation']);
                $this->assertNotEmpty($call['context']['hash']);
                $debugHashFound = true;

                break;
            }
        }
        $this->assertTrue($debugHashFound, 'Debug log for default hash was not found');

        // Verify debug log for request variables
        $debugVarsFound = false;
        foreach ($debugCalls as $call) {
            if ($call['message'] === 'Request variables') {
                $this->assertSame('metGetProductById', $call['context']['operation']);
                $this->assertIsArray($call['context']['variables']);
                $this->assertSame('CUSA12345_00', $call['context']['variables']['productId']);
                $debugVarsFound = true;

                break;
            }
        }
        $this->assertTrue($debugVarsFound, 'Debug log for request variables was not found');
    }

    /**
     * Test handling invalid JSON in response (200 OK but invalid JSON body)
     */
    public function testHandlingInvalidJsonInResponse(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        // Setup response with invalid JSON
        $this->stream->method('getContents')
            ->willReturn('{invalid json}');

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

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $this->client->getProductById($request);
    }

    /**
     * Test handling empty JSON response (200 OK but empty body)
     */
    public function testHandlingEmptyJsonResponse(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        // Setup response with empty JSON
        $this->stream->method('getContents')
            ->willReturn('');

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

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $this->client->getProductById($request);
    }

    /**
     * Test handling truncated JSON response (200 OK but incomplete JSON)
     */
    public function testHandlingTruncatedJsonResponse(): void
    {
        $request = new RequestProductById('CUSA12345_00');

        // Setup response with truncated JSON (missing closing brace)
        $this->stream->method('getContents')
            ->willReturn('{"data":{"productRetrieve":{"id":"CUSA12345_00"');

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

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $this->client->getProductById($request);
    }

    /**
     * Test handling empty errors array (should not throw exception)
     */
    public function testHandlingEmptyErrorsArray(): void
    {
        $request = new RequestProductById('CUSA12345_00');
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
            'errors' => [], // Empty errors array
        ];

        $this->setupSuccessfulRequest($responseData);

        // Should not throw exception - empty errors array should be ignored
        $result = $this->client->getProductById($request);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame('CUSA12345_00', $result->id);
    }

    /**
     * Test handling null errors (should not throw exception)
     */
    public function testHandlingNullErrors(): void
    {
        $request = new RequestProductById('CUSA12345_00');
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
            'errors' => null, // Null errors
        ];

        $this->setupSuccessfulRequest($responseData);

        // Should not throw exception - null errors should be ignored
        $result = $this->client->getProductById($request);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame('CUSA12345_00', $result->id);
    }

    /**
     * Test handling errors key that is not an array (should not throw exception)
     */
    public function testHandlingNonArrayErrors(): void
    {
        $request = new RequestProductById('CUSA12345_00');
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
            'errors' => 'not an array', // Errors is not an array
        ];

        $this->setupSuccessfulRequest($responseData);

        // Should not throw exception - non-array errors should be ignored
        $result = $this->client->getProductById($request);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame('CUSA12345_00', $result->id);
    }

    /**
     * Test logging on denormalization error
     */
    public function testLoggingOnDenormalizationError(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        // Create a custom serializer that throws exception during denormalization
        $denormalizationException = new \RuntimeException('Denormalization failed: invalid data structure');

        $mockSerializer = new class ($denormalizationException) extends Serializer {
            private $exception;

            public function __construct($exception)
            {
                $this->exception = $exception;
                // Call parent with minimal normalizers
                parent::__construct([], [new JsonEncoder()]);
            }

            public function normalize($data, ?string $format = null, array $context = []): array|string|int|float|bool
            {
                if (is_object($data) && method_exists($data, 'productId')) {
                    return ['productId' => $data->productId];
                }

                return [];
            }

            public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
            {
                throw $this->exception;
            }
        };

        $client = new Client(
            RegionEnum::UNITED_STATES,
            $this->httpClient,
            $this->requestFactory,
            $mockSerializer,
            Client::DEFAULT_BASE_URI,
            $logger
        );

        $request = new RequestProductById('CUSA12345_00');
        $responseData = [
            'data' => [
                'productRetrieve' => [
                    'id' => 'CUSA12345_00',
                    'name' => 'Test Game',
                ],
            ],
        ];

        $this->setupSuccessfulRequest($responseData);

        // Capture error calls
        $errorCalls = [];
        $logger->method('error')
            ->willReturnCallback(function ($message, $context) use (&$errorCalls) {
                $errorCalls[] = ['message' => $message, 'context' => $context];
            });

        // Also allow info and debug logs
        $logger->method('info')->willReturnCallback(function () {});
        $logger->method('debug')->willReturnCallback(function () {});

        $this->expectException(PsnApiException::class);
        $this->expectExceptionMessage('Unexpected error: Denormalization failed: invalid data structure');

        $client->getProductById($request);

        // Verify error log for denormalization error
        $denormErrorFound = false;
        foreach ($errorCalls as $call) {
            if ($call['message'] === 'Denormalization error') {
                $this->assertSame('metGetProductById', $call['context']['operation']);
                $this->assertSame(\PlaystationStoreApi\Dto\Product\Product::class, $call['context']['dto_class']);
                $this->assertSame('data.productRetrieve', $call['context']['data_path']);
                $this->assertNotEmpty($call['context']['error']);
                $this->assertIsArray($call['context']['response_data']);
                $denormErrorFound = true;

                break;
            }
        }
        $this->assertTrue($denormErrorFound, 'Error log for denormalization error was not found');
    }

    /**
     * Test logging with overridden hash
     */
    public function testLoggingWithOverriddenHash(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $client = new Client(
            RegionEnum::UNITED_STATES,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            Client::DEFAULT_BASE_URI,
            $logger
        );

        $request = new RequestProductById('CUSA12345_00');
        $newHash = 'custom_hash_value';
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
        $client->overrideSha256Hash('metGetProductById', $newHash);

        // Capture info calls
        $infoCalls = [];
        $logger->method('info')
            ->willReturnCallback(function ($message, $context) use (&$infoCalls) {
                $infoCalls[] = ['message' => $message, 'context' => $context];
            });

        $client->getProductById($request);

        // Verify info log for overridden hash
        $hashInfoFound = false;
        foreach ($infoCalls as $call) {
            if ($call['message'] === 'Using overridden SHA-256 hash for operation') {
                $this->assertSame('metGetProductById', $call['context']['operation']);
                $this->assertSame($newHash, $call['context']['hash']);
                $hashInfoFound = true;

                break;
            }
        }
        $this->assertTrue($hashInfoFound, 'Info log for overridden hash was not found');

        // Verify info log for sending request
        $requestInfoFound = false;
        foreach ($infoCalls as $call) {
            if ($call['message'] === 'Sending request to PlayStation Store API') {
                $requestInfoFound = true;

                break;
            }
        }
        $this->assertTrue($requestInfoFound, 'Info log for sending request was not found');
    }

    /**
     * Test logging context contains correct operation name and variables
     */
    public function testLoggingContextContainsCorrectData(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $capturedInfoContext = null;
        $capturedDebugContext = null;

        $logger->method('info')
            ->willReturnCallback(function ($message, $context) use (&$capturedInfoContext) {
                if ($message === 'Sending request to PlayStation Store API') {
                    $capturedInfoContext = $context;
                }
            });

        $logger->method('debug')
            ->willReturnCallback(function ($message, $context) use (&$capturedDebugContext) {
                if ($message === 'Request variables') {
                    $capturedDebugContext = $context;
                }
            });

        $client = new Client(
            RegionEnum::RUSSIA,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            Client::DEFAULT_BASE_URI,
            $logger
        );

        $request = new RequestProductById('CUSA12345_00');
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

        $client->getProductById($request);

        // Verify info context
        $this->assertNotNull($capturedInfoContext);
        $this->assertSame('metGetProductById', $capturedInfoContext['operation']);
        $this->assertSame('ru-ru', $capturedInfoContext['region']);
        $this->assertStringContainsString('operationName=metGetProductById', $capturedInfoContext['uri']);

        // Verify debug context
        $this->assertNotNull($capturedDebugContext);
        $this->assertSame('metGetProductById', $capturedDebugContext['operation']);
        $this->assertIsArray($capturedDebugContext['variables']);
        $this->assertSame('CUSA12345_00', $capturedDebugContext['variables']['productId']);
    }
}
