<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Concept;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\CompatibilityNotice;
use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\PersonalizedMeta;
use PlaystationStoreApi\Dto\Common\ReleaseDateDescriptor;
use PlaystationStoreApi\Dto\Concept\Concept;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for Concept DTO
 */
final class ConceptTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        // Configure PropertyInfoExtractor to read PHPDoc annotations
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
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            $objectNormalizer,
        ], [new JsonEncoder()]);
    }

    public function testDeserializeConceptWithReleaseDate(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "invariantName": "test-concept",
            "releaseDate": {
                "type": "DAY_MONTH_YEAR",
                "value": "2023-01-01T00:00:00Z"
            }
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertSame('10002694', $concept->id);
        $this->assertSame('Test Concept', $concept->name);
        $this->assertInstanceOf(ReleaseDateDescriptor::class, $concept->releaseDate);
        $this->assertSame('DAY_MONTH_YEAR', $concept->releaseDate->type);
        $this->assertSame('2023-01-01T00:00:00Z', $concept->releaseDate->value);
    }

    public function testDeserializeConceptWithCompatibilityNotices(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "compatibilityNotices": [
                {
                    "type": "NO_OF_PLAYERS",
                    "value": "1"
                },
                {
                    "type": "PS5_VIBRATION",
                    "value": "OPTIONAL"
                },
                {
                    "type": "PS5_ADAPTIVE_TRIGGERS",
                    "value": "true"
                }
            ]
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertIsArray($concept->compatibilityNotices);
        $this->assertCount(3, $concept->compatibilityNotices);
        $this->assertContainsOnlyInstancesOf(CompatibilityNotice::class, $concept->compatibilityNotices);

        $this->assertSame('NO_OF_PLAYERS', $concept->compatibilityNotices[0]->type);
        $this->assertSame('1', $concept->compatibilityNotices[0]->value);

        $this->assertSame('PS5_VIBRATION', $concept->compatibilityNotices[1]->type);
        $this->assertSame('OPTIONAL', $concept->compatibilityNotices[1]->value);

        $this->assertSame('PS5_ADAPTIVE_TRIGGERS', $concept->compatibilityNotices[2]->type);
        $this->assertSame('true', $concept->compatibilityNotices[2]->value);
    }

    public function testDeserializeConceptWithPersonalizedMeta(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "personalizedMeta": {
                "__typename": "PersonalizedMeta",
                "hasMediaOverrides": true,
                "media": [
                    {
                        "__typename": "Media",
                        "role": "MASTER",
                        "type": "SCREENSHOT",
                        "url": "https://example.com/personalized.jpg",
                        "media": null
                    }
                ]
            }
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertInstanceOf(PersonalizedMeta::class, $concept->personalizedMeta);
        $this->assertSame('PersonalizedMeta', $concept->personalizedMeta->__typename);
        $this->assertTrue($concept->personalizedMeta->hasMediaOverrides);
        $this->assertIsArray($concept->personalizedMeta->media);
        $this->assertCount(1, $concept->personalizedMeta->media);
        $this->assertContainsOnlyInstancesOf(Media::class, $concept->personalizedMeta->media);
        $this->assertSame('MASTER', $concept->personalizedMeta->media[0]->role);
        $this->assertSame('SCREENSHOT', $concept->personalizedMeta->media[0]->type);
    }

    public function testDeserializeConceptWithAllComplexFields(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "invariantName": "test-concept",
            "releaseDate": {
                "type": "DAY_MONTH_YEAR",
                "value": "2023-01-01T00:00:00Z"
            },
            "compatibilityNotices": [
                {
                    "type": "NO_OF_PLAYERS",
                    "value": "1"
                },
                {
                    "type": "PS5_VIBRATION",
                    "value": "OPTIONAL"
                }
            ],
            "personalizedMeta": {
                "__typename": "PersonalizedMeta",
                "hasMediaOverrides": false,
                "media": null
            }
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertInstanceOf(ReleaseDateDescriptor::class, $concept->releaseDate);
        $this->assertIsArray($concept->compatibilityNotices);
        $this->assertCount(2, $concept->compatibilityNotices);
        $this->assertInstanceOf(PersonalizedMeta::class, $concept->personalizedMeta);
    }

    public function testDeserializeConceptWithNullComplexFields(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "releaseDate": null,
            "compatibilityNotices": null,
            "personalizedMeta": null
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertNull($concept->releaseDate);
        $this->assertNull($concept->compatibilityNotices);
        $this->assertNull($concept->personalizedMeta);
    }

    public function testDeserializeConceptWithEmptyCompatibilityNotices(): void
    {
        $json = '{
            "id": "10002694",
            "name": "Test Concept",
            "compatibilityNotices": []
        }';

        $concept = $this->serializer->deserialize($json, Concept::class, 'json');

        $this->assertInstanceOf(Concept::class, $concept);
        $this->assertIsArray($concept->compatibilityNotices);
        $this->assertEmpty($concept->compatibilityNotices);
    }
}
