<?php

declare(strict_types=1);

namespace PlaystationStoreApi;

use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Serializer\PlaystationResponseDenormalizer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
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
     */
    public static function create(
        RegionEnum $region,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?SerializerInterface $serializer = null
    ): Client {
        $httpClient     = $httpClient ?? self::createDefaultHttpClient();
        $requestFactory = $requestFactory ?? self::createDefaultRequestFactory();
        $serializer     = $serializer ?? self::createDefaultSerializer();

        return new Client($region, $httpClient, $requestFactory, $serializer);
    }

    /**
     * Create default HTTP client (requires PSR-18 implementation)
     */
    private static function createDefaultHttpClient(): ClientInterface
    {
        // Try Guzzle HTTP client
        if (class_exists(\GuzzleHttp\Client::class)) {
            return new \GuzzleHttp\Client();
        }

        // Try Symfony HTTP client
        if (class_exists(\Symfony\Component\HttpClient\HttpClient::class)) {
            $httpClient = \Symfony\Component\HttpClient\HttpClient::create();
            // Check if Psr18Client exists (requires symfony/http-client ^6.3 or ^7.0)
            if (class_exists(\Symfony\Component\HttpClient\Psr18Client::class)) {
                /** @psalm-suppress UndefinedClass */
                /** @phpstan-ignore-next-line */
                return new \Symfony\Component\HttpClient\Psr18Client($httpClient);
            }
        }

        throw new \RuntimeException(
            'HTTP client must be provided. Install a PSR-18 implementation like guzzlehttp/guzzle or symfony/http-client'
        );
    }

    /**
     * Create default request factory (requires PSR-17 implementation)
     */
    private static function createDefaultRequestFactory(): RequestFactoryInterface
    {
        // Try Nyholm PSR7
        if (class_exists(\Nyholm\Psr7\Factory\Psr17Factory::class)) {
            return new \Nyholm\Psr7\Factory\Psr17Factory();
        }

        // Try Guzzle PSR7
        if (class_exists(\GuzzleHttp\Psr7\HttpFactory::class)) {
            return new \GuzzleHttp\Psr7\HttpFactory();
        }

        throw new \RuntimeException(
            'Request factory must be provided. Install a PSR-17 implementation like nyholm/psr7 or guzzlehttp/psr7'
        );
    }

    /**
     * Create default Symfony Serializer with proper configuration
     */
    private static function createDefaultSerializer(): SerializerInterface
    {
        $encoders = [new JsonEncoder()];

        // Configure PropertyInfoExtractor to read PHPDoc annotations
        $phpDocExtractor     = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo        = new PropertyInfoExtractor(
            [$reflectionExtractor], // PropertyListExtractorInterface
            [$phpDocExtractor, $reflectionExtractor], // PropertyTypeExtractorInterface (PhpDocExtractor has priority)
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
            $objectNormalizer
        ];

        return new Serializer($normalizers, $encoders);
    }
}
