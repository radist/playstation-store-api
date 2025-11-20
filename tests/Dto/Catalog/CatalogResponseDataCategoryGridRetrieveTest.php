<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Test\Dto\Catalog;

use PHPUnit\Framework\TestCase;
use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Dto\Catalog\CategoryFacet;
use PlaystationStoreApi\Dto\Catalog\CategoryFacetValue;
use PlaystationStoreApi\Dto\Catalog\CategorySortingOption;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;
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
 * Test for CatalogResponseDataCategoryGridRetrieve
 */
final class CatalogResponseDataCategoryGridRetrieveTest extends TestCase
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
    /**
     * Test getAllProducts returns empty array when concepts is null
     */
    public function testGetAllProductsReturnsEmptyArrayWhenConceptsIsNull(): void
    {
        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: null
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getAllProducts returns empty array when concepts contains elements but products is null
     */
    public function testGetAllProductsReturnsEmptyArrayWhenConceptsProductsIsNull(): void
    {
        $concept1 = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: null
        );

        $concept2 = new Concept(
            id: '10002695',
            name: 'Concept 2',
            products: null
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept1, $concept2]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getAllProducts returns empty array when some concepts have null products
     */
    public function testGetAllProductsReturnsEmptyArrayWhenSomeConceptsHaveNullProducts(): void
    {
        $concept1 = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: null
        );

        $concept2 = new Concept(
            id: '10002695',
            name: 'Concept 2',
            products: null
        );

        $concept3 = new Concept(
            id: '10002696',
            name: 'Concept 3',
            products: null
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept1, $concept2, $concept3]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getAllProducts correctly merges products from multiple concepts
     */
    public function testGetAllProductsCorrectlyMergesProductsFromMultipleConcepts(): void
    {
        $product1 = new Product(
            id: 'CUSA12345_00',
            name: 'Product 1'
        );

        $product2 = new Product(
            id: 'CUSA12346_00',
            name: 'Product 2'
        );

        $product3 = new Product(
            id: 'CUSA12347_00',
            name: 'Product 3'
        );

        $concept1 = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: [$product1, $product2]
        );

        $concept2 = new Concept(
            id: '10002695',
            name: 'Concept 2',
            products: [$product3]
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept1, $concept2]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame($product1, $result[0]);
        $this->assertSame($product2, $result[1]);
        $this->assertSame($product3, $result[2]);
    }

    /**
     * Test getAllProducts correctly merges products when some concepts have null products
     */
    public function testGetAllProductsCorrectlyMergesWhenSomeConceptsHaveNullProducts(): void
    {
        $product1 = new Product(
            id: 'CUSA12345_00',
            name: 'Product 1'
        );

        $product2 = new Product(
            id: 'CUSA12346_00',
            name: 'Product 2'
        );

        $concept1 = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: null
        );

        $concept2 = new Concept(
            id: '10002695',
            name: 'Concept 2',
            products: [$product1, $product2]
        );

        $concept3 = new Concept(
            id: '10002696',
            name: 'Concept 3',
            products: null
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept1, $concept2, $concept3]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame($product1, $result[0]);
        $this->assertSame($product2, $result[1]);
    }

    /**
     * Test getAllProducts with single concept containing products
     */
    public function testGetAllProductsWithSingleConceptContainingProducts(): void
    {
        $product1 = new Product(
            id: 'CUSA12345_00',
            name: 'Product 1'
        );

        $product2 = new Product(
            id: 'CUSA12346_00',
            name: 'Product 2'
        );

        $concept = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: [$product1, $product2]
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame($product1, $result[0]);
        $this->assertSame($product2, $result[1]);
    }

    /**
     * Test getAllProducts with empty concepts array
     */
    public function testGetAllProductsWithEmptyConceptsArray(): void
    {
        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: []
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getAllProducts with multiple concepts and multiple products each
     */
    public function testGetAllProductsWithMultipleConceptsAndMultipleProductsEach(): void
    {
        $product1 = new Product(id: 'CUSA12345_00', name: 'Product 1');
        $product2 = new Product(id: 'CUSA12346_00', name: 'Product 2');
        $product3 = new Product(id: 'CUSA12347_00', name: 'Product 3');
        $product4 = new Product(id: 'CUSA12348_00', name: 'Product 4');
        $product5 = new Product(id: 'CUSA12349_00', name: 'Product 5');

        $concept1 = new Concept(
            id: '10002694',
            name: 'Concept 1',
            products: [$product1, $product2]
        );

        $concept2 = new Concept(
            id: '10002695',
            name: 'Concept 2',
            products: [$product3, $product4, $product5]
        );

        $catalog = new CatalogResponseDataCategoryGridRetrieve(
            concepts: [$concept1, $concept2]
        );

        $result = $catalog->getAllProducts();

        $this->assertIsArray($result);
        $this->assertCount(5, $result);
        $this->assertSame($product1, $result[0]);
        $this->assertSame($product2, $result[1]);
        $this->assertSame($product3, $result[2]);
        $this->assertSame($product4, $result[3]);
        $this->assertSame($product5, $result[4]);
    }

    /**
     * Test deserialize CatalogResponseDataCategoryGridRetrieve with facetOptions
     */
    public function testDeserializeCatalogWithFacetOptions(): void
    {
        $json = '{
            "id": "cat.gma.NewGames",
            "localizedName": "New Games",
            "facetOptions": [
                {
                    "name": "conceptGenres",
                    "displayName": "Genre",
                    "values": [
                        {
                            "key": "ACTION",
                            "displayName": "Action",
                            "count": 98
                        },
                        {
                            "key": "ADVENTURE",
                            "displayName": "Adventure",
                            "count": 45
                        }
                    ]
                },
                {
                    "name": "priceRange",
                    "displayName": "Price",
                    "values": [
                        {
                            "key": "0-199",
                            "displayName": "Under $1.99",
                            "count": 12
                        }
                    ]
                }
            ]
        }';

        $catalog = $this->serializer->deserialize($json, CatalogResponseDataCategoryGridRetrieve::class, 'json');

        $this->assertInstanceOf(CatalogResponseDataCategoryGridRetrieve::class, $catalog);
        $this->assertIsArray($catalog->facetOptions);
        $this->assertCount(2, $catalog->facetOptions);
        $this->assertContainsOnlyInstancesOf(CategoryFacet::class, $catalog->facetOptions);

        $genreFacet = $catalog->facetOptions[0];
        $this->assertSame('conceptGenres', $genreFacet->name);
        $this->assertSame('Genre', $genreFacet->displayName);
        $this->assertIsArray($genreFacet->values);
        $this->assertCount(2, $genreFacet->values);
        $this->assertContainsOnlyInstancesOf(CategoryFacetValue::class, $genreFacet->values);
        $this->assertSame('ACTION', $genreFacet->values[0]->key);
        $this->assertSame('Action', $genreFacet->values[0]->displayName);
        $this->assertSame(98, $genreFacet->values[0]->count);

        $priceFacet = $catalog->facetOptions[1];
        $this->assertSame('priceRange', $priceFacet->name);
        $this->assertSame('Price', $priceFacet->displayName);
        $this->assertCount(1, $priceFacet->values);
        $this->assertSame('0-199', $priceFacet->values[0]->key);
        $this->assertSame('Under $1.99', $priceFacet->values[0]->displayName);
        $this->assertSame(12, $priceFacet->values[0]->count);
    }

    /**
     * Test deserialize CatalogResponseDataCategoryGridRetrieve with sortingOptions
     */
    public function testDeserializeCatalogWithSortingOptions(): void
    {
        $json = '{
            "id": "cat.gma.NewGames",
            "localizedName": "New Games",
            "sortingOptions": [
                {
                    "name": "productReleaseDate",
                    "displayName": "Release Date (New - Old)",
                    "isAscending": false
                },
                {
                    "name": "sales30",
                    "displayName": "Bestsellers",
                    "isAscending": null
                },
                {
                    "name": "productName",
                    "displayName": "Name (A - Z)",
                    "isAscending": true
                }
            ],
            "sortedBy": {
                "name": "productReleaseDate",
                "displayName": "Release Date (New - Old)",
                "isAscending": false
            }
        }';

        $catalog = $this->serializer->deserialize($json, CatalogResponseDataCategoryGridRetrieve::class, 'json');

        $this->assertInstanceOf(CatalogResponseDataCategoryGridRetrieve::class, $catalog);
        $this->assertIsArray($catalog->sortingOptions);
        $this->assertCount(3, $catalog->sortingOptions);
        $this->assertContainsOnlyInstancesOf(CategorySortingOption::class, $catalog->sortingOptions);

        $this->assertSame('productReleaseDate', $catalog->sortingOptions[0]->name);
        $this->assertSame('Release Date (New - Old)', $catalog->sortingOptions[0]->displayName);
        $this->assertFalse($catalog->sortingOptions[0]->isAscending);

        $this->assertSame('sales30', $catalog->sortingOptions[1]->name);
        $this->assertSame('Bestsellers', $catalog->sortingOptions[1]->displayName);
        $this->assertNull($catalog->sortingOptions[1]->isAscending);

        $this->assertSame('productName', $catalog->sortingOptions[2]->name);
        $this->assertSame('Name (A - Z)', $catalog->sortingOptions[2]->displayName);
        $this->assertTrue($catalog->sortingOptions[2]->isAscending);

        $this->assertInstanceOf(CategorySortingOption::class, $catalog->sortedBy);
        $this->assertSame('productReleaseDate', $catalog->sortedBy->name);
        $this->assertFalse($catalog->sortedBy->isAscending);
    }

    /**
     * Test deserialize CatalogResponseDataCategoryGridRetrieve with null facetOptions and sortingOptions
     */
    public function testDeserializeCatalogWithNullFacetOptionsAndSortingOptions(): void
    {
        $json = '{
            "id": "cat.gma.NewGames",
            "localizedName": "New Games",
            "facetOptions": null,
            "sortingOptions": null,
            "sortedBy": null
        }';

        $catalog = $this->serializer->deserialize($json, CatalogResponseDataCategoryGridRetrieve::class, 'json');

        $this->assertInstanceOf(CatalogResponseDataCategoryGridRetrieve::class, $catalog);
        $this->assertNull($catalog->facetOptions);
        $this->assertNull($catalog->sortingOptions);
        $this->assertNull($catalog->sortedBy);
    }

    /**
     * Test deserialize CatalogResponseDataCategoryGridRetrieve with empty facetOptions and sortingOptions
     */
    public function testDeserializeCatalogWithEmptyFacetOptionsAndSortingOptions(): void
    {
        $json = '{
            "id": "cat.gma.NewGames",
            "localizedName": "New Games",
            "facetOptions": [],
            "sortingOptions": []
        }';

        $catalog = $this->serializer->deserialize($json, CatalogResponseDataCategoryGridRetrieve::class, 'json');

        $this->assertInstanceOf(CatalogResponseDataCategoryGridRetrieve::class, $catalog);
        $this->assertIsArray($catalog->facetOptions);
        $this->assertEmpty($catalog->facetOptions);
        $this->assertIsArray($catalog->sortingOptions);
        $this->assertEmpty($catalog->sortingOptions);
    }
}
