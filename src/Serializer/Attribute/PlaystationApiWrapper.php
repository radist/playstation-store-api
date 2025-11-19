<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Serializer\Attribute;

use Attribute;

/**
 * Attribute to mark wrapper classes that should be skipped by PlaystationResponseDenormalizer.
 *
 * Wrapper classes are simple classes that only contain a 'data' property
 * and should not be processed by the denormalizer.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class PlaystationApiWrapper
{
}
