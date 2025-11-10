<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Request;

/**
 * Base interface for all PlayStation Store API requests
 */
interface BaseRequest
{
    /**
     * Get the expected DTO class for this request type
     *
     * @return class-string
     */
    public function getResponseDtoClass(): string;

    /**
     * Get the operation name for this request
     *
     * @return string
     */
    public function getOperationName(): string;

    /**
     * Get the SHA-256 hash for this request
     *
     * @return string
     */
    public function getSha256Hash(): string;

    /**
     * Returns the path to data inside the DTO wrapper response.
     * @return string For example: 'data.productRetrieve'
     */
    public function getDataPath(): string;
}
