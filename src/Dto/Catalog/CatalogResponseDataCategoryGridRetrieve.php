<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

use PlaystationStoreApi\Dto\Common\PageInfo;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Category grid retrieve data containing products and concepts
 */
final readonly class CatalogResponseDataCategoryGridRetrieve
{
    /**
     * @param Product[]|null $products
     * @param Concept[]|null $concepts
     */
    public function __construct(
        public ?string $id = null,
        /** @var Product[]|null */
        public ?array $products = null,
        /** @var Concept[]|null */
        public ?array $concepts = null,
        public ?PageInfo $pageInfo = null,
    ) {
    }
}
