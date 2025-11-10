<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Common;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\Media;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for Media DTO
 */
final class MediaTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
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
            new ArrayDenormalizer(),
            $objectNormalizer,
        ], [new JsonEncoder()]);
    }

    public function testDeserializeValidMedia(): void
    {
        $json = '{
            "role": "MASTER",
            "type": "SCREENSHOT",
            "url": "https://example.com/image.jpg"
        }';

        $media = $this->serializer->deserialize($json, Media::class, 'json');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertSame('MASTER', $media->role);
        $this->assertSame('SCREENSHOT', $media->type);
        $this->assertSame('https://example.com/image.jpg', $media->url);
    }

    public function testDeserializeMediaWithNullValues(): void
    {
        $json = '{
            "role": null,
            "type": null,
            "url": null
        }';

        $media = $this->serializer->deserialize($json, Media::class, 'json');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertNull($media->role);
        $this->assertNull($media->type);
        $this->assertNull($media->url);
    }

    public function testDeserializeMediaWithPartialData(): void
    {
        $json = '{
            "url": "https://example.com/video.mp4"
        }';

        $media = $this->serializer->deserialize($json, Media::class, 'json');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertNull($media->role);
        $this->assertNull($media->type);
        $this->assertSame('https://example.com/video.mp4', $media->url);
    }

    public function testDeserializeMediaWithNestedMedia(): void
    {
        $json = '{
            "__typename": "Media",
            "role": "PREVIEW",
            "type": "VIDEO",
            "url": "https://example.com/video.mp4",
            "media": [
                {
                    "__typename": "Media",
                    "role": "SCREENSHOT",
                    "type": "IMAGE",
                    "url": "https://example.com/frame1.jpg"
                },
                {
                    "__typename": "Media",
                    "role": "SCREENSHOT",
                    "type": "IMAGE",
                    "url": "https://example.com/frame2.jpg"
                }
            ]
        }';

        $media = $this->serializer->deserialize($json, Media::class, 'json');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertSame('Media', $media->__typename);
        $this->assertSame('PREVIEW', $media->role);
        $this->assertSame('VIDEO', $media->type);
        $this->assertSame('https://example.com/video.mp4', $media->url);
        $this->assertIsArray($media->media);
        $this->assertCount(2, $media->media);
        $this->assertContainsOnlyInstancesOf(Media::class, $media->media);
        $this->assertSame('SCREENSHOT', $media->media[0]->role);
        $this->assertSame('IMAGE', $media->media[0]->type);
        $this->assertSame('https://example.com/frame1.jpg', $media->media[0]->url);
    }
}
