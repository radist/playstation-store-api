<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Catalog;

use PlaystationStoreApi\Dto\Common\PageInfo;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;

/**
 * Category grid retrieve data containing concepts, facets, and sorting info
 */
final readonly class CatalogResponseDataCategoryGridRetrieve
{
    /**
     * @param Product[]|null $products Usually empty in this query - products are nested inside concepts
     * @param Concept[]|null $concepts Array of concepts, each containing products
     * @param CategoryFacet[]|null $facetOptions Available filters
     * @param CategorySortingOption[]|null $sortingOptions Available sorting options
     */
    public function __construct(
        public ?string $id = null,
        public ?string $localizedName = null, // e.g. "cat.gma.NewGames"
        public ?string $reportingName = null, // e.g. "GMA_NEW_GAMES"

        /** @var Product[]|null */
        public ?array $products = null,

        /** @var Concept[]|null */
        public ?array $concepts = null,
        public ?PageInfo $pageInfo = null,

        /** @var CategoryFacet[]|null */
        public ?array $facetOptions = null, // Added: Filters

        /** @var CategorySortingOption[]|null */
        public ?array $sortingOptions = null, // Added: Available sorts

        public ?CategorySortingOption $sortedBy = null, // Added: Current sort
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
