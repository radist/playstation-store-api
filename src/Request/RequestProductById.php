<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

use PlaystationStoreApi\Dto\Product\Product;
use PlaystationStoreApi\Enum\OperationSha256Enum;

/**
 * Request for getting a product by its ID
 */
final class RequestProductById implements BaseRequest
{
    public function __construct(public readonly string $productId)
    {
    }

    public function getResponseDtoClass(): string
    {
        return Product::class;
    }

    public function getOperationName(): string
    {
        return OperationSha256Enum::metGetProductById->name;
    }

    public function getSha256Hash(): string
    {
        return OperationSha256Enum::metGetProductById->value;
    }

    public function getDataPath(): string
    {
        return 'data.productRetrieve';
    }
}
