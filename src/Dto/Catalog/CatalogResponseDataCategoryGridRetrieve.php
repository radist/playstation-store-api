<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

use PlaystationStoreApi\Dto\Common\PageInfo;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Category grid retrieve data containing concepts
 *
 * Note: API returns concepts array, not products directly.
 * Each concept contains a products array with related products.
 */
final readonly class CatalogResponseDataCategoryGridRetrieve
{
    /**
     * @param Product[]|null $products Usually empty - products are nested inside concepts
     * @param Concept[]|null $concepts Array of concepts, each containing products
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

    /**
     * Extract all products from all concepts
     *
     * @return Product[]
     */
    public function getAllProducts(): array
    {
        if ($this->concepts === null) {
            return [];
        }

        $allProducts = [];
        foreach ($this->concepts as $concept) {
            if ($concept->products !== null) {
                $allProducts = array_merge($allProducts, $concept->products);
            }
        }

        return $allProducts;
    }
}
