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
     * Extract all products from both direct products array and concepts
     *
     * @return Product[]
     */
    public function getAllProducts(): array
    {
        $allProducts = [];

        // First, add products from direct products array (PS4/PS5 categories)
        if ($this->products !== null) {
            $allProducts = array_merge($allProducts, $this->products);
        }

        // Then, extract products from concepts (NEW_GAMES category)
        if ($this->concepts !== null) {
            foreach ($this->concepts as $concept) {
                if ($concept->products !== null) {
                    $allProducts = array_merge($allProducts, $concept->products);
                }
            }
        }

        return $allProducts;
    }
}
