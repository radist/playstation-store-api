<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Serializer;

use PlaystationStoreApi\Serializer\Attribute\PlaystationApiWrapper;

/**
 * Test wrapper class with PlaystationApiWrapper attribute
 * Used for testing that PlaystationResponseDenormalizer skips wrapper classes
 */
#[PlaystationApiWrapper]
final readonly class TestWrapperClass
{
    public function __construct(
        public ?array $data = null,
    ) {
    }
}
