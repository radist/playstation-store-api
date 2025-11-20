<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\ClientFactory;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\PlaystationStoreClientInterface;
use PlaystationStoreApi\Serializer\EnumNormalizer;
use PlaystationStoreApi\Serializer\PlaystationResponseDenormalizer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
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
 * Test for ClientFactory
 */
final class ClientFactoryTest extends TestCase
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);

        // Create serializer similar to ClientFactory::createDefaultSerializer()
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
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

        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testCreateWithExplicitDependencies(): void
    {
        $region = RegionEnum::UNITED_STATES;
        $baseUri = 'https://custom.api.example.com/';

        $client = ClientFactory::create(
            $region,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            $baseUri,
            $this->logger
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithExplicitHttpClientOnly(): void
    {
        $region = RegionEnum::RUSSIA;

        $client = ClientFactory::create(
            $region,
            $this->httpClient
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithExplicitRequestFactoryOnly(): void
    {
        $region = RegionEnum::UNITED_STATES;

        $client = ClientFactory::create(
            $region,
            null,
            $this->requestFactory
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithExplicitSerializerOnly(): void
    {
        $region = RegionEnum::UNITED_STATES;

        $client = ClientFactory::create(
            $region,
            null,
            null,
            $this->serializer
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithAutoDiscovery(): void
    {
        $region = RegionEnum::UNITED_STATES;

        // This test assumes PSR-18 and PSR-17 implementations are available
        // (which should be the case in test environment with guzzlehttp/guzzle and nyholm/psr7)
        $client = ClientFactory::create($region);

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithCustomBaseUri(): void
    {
        $region = RegionEnum::UNITED_STATES;
        $customBaseUri = 'https://custom.example.com/api/';

        $client = ClientFactory::create(
            $region,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            $customBaseUri
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithLogger(): void
    {
        $region = RegionEnum::UNITED_STATES;

        $client = ClientFactory::create(
            $region,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            null,
            $this->logger
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateWithAllParameters(): void
    {
        $region = RegionEnum::UNITED_STATES;
        $baseUri = 'https://test.example.com/api/';

        $client = ClientFactory::create(
            $region,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            $baseUri,
            $this->logger
        );

        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);
    }

    public function testCreateReturnsCorrectInterface(): void
    {
        $region = RegionEnum::UNITED_STATES;

        $client = ClientFactory::create(
            $region,
            $this->httpClient,
            $this->requestFactory,
            $this->serializer
        );

        // Verify it implements the interface
        $this->assertInstanceOf(PlaystationStoreClientInterface::class, $client);

        // Verify it's not null
        $this->assertNotNull($client);
    }
}
