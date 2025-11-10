<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Common;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Common\PageInfo;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Test for PageInfo DTO
 */
final class PageInfoTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer([
            new ObjectNormalizer(
                null,
                null,
                PropertyAccess::createPropertyAccessor()
            ),
        ], [new JsonEncoder()]);
    }

    public function testDeserializeValidPageInfo(): void
    {
        $json = '{
            "totalCount": 150,
            "isLast": false,
            "offset": 0,
            "size": 20
        }';

        $pageInfo = $this->serializer->deserialize($json, PageInfo::class, 'json');

        $this->assertInstanceOf(PageInfo::class, $pageInfo);
        $this->assertSame(150, $pageInfo->totalCount);
        $this->assertFalse($pageInfo->isLast);
        $this->assertSame(0, $pageInfo->offset);
        $this->assertSame(20, $pageInfo->size);
    }

    public function testDeserializePageInfoWithNullValues(): void
    {
        $json = '{
            "totalCount": null,
            "isLast": null,
            "offset": null,
            "size": null
        }';

        $pageInfo = $this->serializer->deserialize($json, PageInfo::class, 'json');

        $this->assertInstanceOf(PageInfo::class, $pageInfo);
        $this->assertNull($pageInfo->totalCount);
        $this->assertNull($pageInfo->isLast);
        $this->assertNull($pageInfo->offset);
        $this->assertNull($pageInfo->size);
    }

    public function testDeserializeLastPage(): void
    {
        $json = '{
            "totalCount": 100,
            "isLast": true,
            "offset": 80,
            "size": 20
        }';

        $pageInfo = $this->serializer->deserialize($json, PageInfo::class, 'json');

        $this->assertInstanceOf(PageInfo::class, $pageInfo);
        $this->assertSame(100, $pageInfo->totalCount);
        $this->assertTrue($pageInfo->isLast);
        $this->assertSame(80, $pageInfo->offset);
        $this->assertSame(20, $pageInfo->size);
    }
}
