<?php

declare(strict_types=1);

namespace PlaystationStoreApi;

use Http\Discovery\Exception\NotFoundException as DiscoveryNotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use PlaystationStoreApi\Enum\RegionEnum;
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
 * Factory for creating Client instances with proper dependencies
 */
final class ClientFactory
{
    /**
     * Create a Client instance with default dependencies
     *
     * @param RegionEnum $region Region enum for API requests
     * @param ClientInterface|null $httpClient Optional PSR-18 HTTP client (auto-detected if not provided)
     * @param RequestFactoryInterface|null $requestFactory Optional PSR-17 request factory (auto-detected if not provided)
     * @param SerializerInterface|null $serializer Optional Symfony serializer (auto-created if not provided)
     * @param string|null $baseUri Optional base URI for API endpoint (default: 'https://web.np.playstation.com/api/graphql/v1/')
     * @param LoggerInterface|null $logger Optional PSR-3 logger for debugging
     */
    public static function create(
        RegionEnum $region,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?SerializerInterface $serializer = null,
        ?string $baseUri = null,
        ?LoggerInterface $logger = null
    ): PlaystationStoreClientInterface {
        $httpClient = $httpClient ?? self::createDefaultHttpClient();
        $requestFactory = $requestFactory ?? self::createDefaultRequestFactory();
        $serializer = $serializer ?? self::createDefaultSerializer();

        return new Client($region, $httpClient, $requestFactory, $serializer, $baseUri ?? Client::DEFAULT_BASE_URI, $logger);
    }

    /**
     * Create default HTTP client (requires PSR-18 implementation)
     *
     * Uses php-http/discovery to automatically find installed PSR-18 implementations.
     *
     * @return ClientInterface
     * @psalm-return ClientInterface
     * @phpstan-return ClientInterface
     * @throws \RuntimeException If no PSR-18 implementation is found
     */
    private static function createDefaultHttpClient(): ClientInterface
    {
        try {
            return Psr18ClientDiscovery::find();
        } catch (DiscoveryNotFoundException $e) {
            throw new \RuntimeException(
                'HTTP client must be provided. Install a PSR-18 implementation like guzzlehttp/guzzle or symfony/http-client',
                0,
                $e
            );
        }
    }

    /**
     * Create default request factory (requires PSR-17 implementation)
     *
     * Uses php-http/discovery to automatically find installed PSR-17 implementations.
     *
     * @return RequestFactoryInterface
     * @psalm-return RequestFactoryInterface
     * @phpstan-return RequestFactoryInterface
     * @throws \RuntimeException If no PSR-17 implementation is found
     */
    private static function createDefaultRequestFactory(): RequestFactoryInterface
    {
        try {
            return Psr17FactoryDiscovery::findRequestFactory();
        } catch (DiscoveryNotFoundException $e) {
            throw new \RuntimeException(
                'Request factory must be provided. Install a PSR-17 implementation like nyholm/psr7 or guzzlehttp/psr7',
                0,
                $e
            );
        }
    }

    /**
     * Create default Symfony Serializer with proper configuration
     */
    private static function createDefaultSerializer(): SerializerInterface
    {
        $encoders = [new JsonEncoder()];

        // Configure PropertyInfoExtractor to read PHPDoc annotations
        // PhpDocExtractor requires phpdocumentor/reflection-docblock (already in composer.json)
        // PhpDocExtractor has priority (first in array) to read @var annotations like "Media[]|null"
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor], // PropertyListExtractorInterface
            [$phpDocExtractor, $reflectionExtractor], // PropertyTypeExtractorInterface (PhpDocExtractor has priority - first in array)
            [$phpDocExtractor], // PropertyDescriptionExtractorInterface
            [$reflectionExtractor], // PropertyAccessExtractorInterface
            [$reflectionExtractor] // PropertyInitializableExtractorInterface
        );

        // Configure ObjectNormalizer with PropertyInfoExtractor
        $objectNormalizer = new ObjectNormalizer(
            null,
            null,
            new PropertyAccessor(),
            $propertyInfo
        );

        $normalizers = [
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new PlaystationResponseDenormalizer($objectNormalizer),
            $objectNormalizer,
        ];

        return new Serializer($normalizers, $encoders);
    }
}
