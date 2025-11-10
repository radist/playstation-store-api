<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\AddOns;

use PlaystationStoreApi\Dto\Common\PageInfo;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Add-ons response data for products by title ID retrieve
 */
final readonly class AddOnsResponseDataAddOnProductsByTitleIdRetrieve
{
    /**
     * @param Product[]|null $addOnProducts
     */
    public function __construct(
        /** @var Product[]|null */
        public ?array $addOnProducts = null,
        public ?PageInfo $pageInfo = null,
    ) {
    }
}
