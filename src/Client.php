<?php

declare(strict_types=1);

namespace PlaystationStoreApi;

use Exception;
use JsonException;
use PlaystationStoreApi\Dto\AddOns\AddOnsResponseDataAddOnProductsByTitleIdRetrieve;
use PlaystationStoreApi\Dto\Catalog\CatalogResponseDataCategoryGridRetrieve;
use PlaystationStoreApi\Dto\Concept\Concept;
use PlaystationStoreApi\Dto\Product\Product;
use PlaystationStoreApi\Dto\Subscription\PSPlusOffersResponseDataTierSelectorOffersRetrieve;
use PlaystationStoreApi\Enum\RegionEnum;
use PlaystationStoreApi\Exception\HttpExceptionFactory;
use PlaystationStoreApi\Exception\PsnApiException;
use PlaystationStoreApi\Request\BaseRequest;
use PlaystationStoreApi\Request\RequestAddOnsByTitleId;
use PlaystationStoreApi\Request\RequestConceptById;
use PlaystationStoreApi\Request\RequestConceptByProductId;
use PlaystationStoreApi\Request\RequestConceptStarRating;
use PlaystationStoreApi\Request\RequestPricingDataByConceptId;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductList;
use PlaystationStoreApi\Request\RequestProductStarRating;
use PlaystationStoreApi\Request\RequestPSPlusTier;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class Client
{
    public const HEADER_CONTENT_TYPE = 'application/json';
    public const DEFAULT_BASE_URI = 'https://web.np.playstation.com/api/graphql/v1/';

    /** @var array<string, string> */
    private array $hashOverrides = [];

    public function __construct(
        private readonly RegionEnum $regionEnum,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly SerializerInterface $serializer,
        private readonly string $baseUri = self::DEFAULT_BASE_URI
    ) {
    }

    /**
     * Override SHA-256 hash for a specific operation
     *
     * @param string $operationName Operation name (e.g., 'metGetProductById')
     * @param string $newHash New SHA-256 hash
     */
    public function overrideSha256Hash(string $operationName, string $newHash): void
    {
        $this->hashOverrides[$operationName] = $newHash;
    }

    /**
     * Execute a request and return the denormalized DTO
     *
     * @throws PsnApiException
     */
    public function execute(BaseRequest $request): object
    {
        try {
            $response = $this->getResponse($request);
            $responseData = $this->decodeResponse($response);

            // Check for API errors in response
            if (isset($responseData['errors']) && is_array($responseData['errors']) && count($responseData['errors']) > 0) {
                /** @var array<int, array<string, mixed>> $errors */
                $errors = $responseData['errors'];
                $this->handleApiErrors($errors, $response->getStatusCode());
            }

            $dtoClass = $request->getResponseDtoClass();
            $dataPath = $request->getDataPath();

            // Deserialize directly to target DTO with dataPath in context
            $responseDto = $this->deserializeResponse($responseData, $dtoClass, $dataPath);

            if (! is_object($responseDto)) {
                throw HttpExceptionFactory::create(
                    500,
                    'Failed to deserialize response to object',
                    null
                );
            }

            return $responseDto;

        } catch (JsonException $e) {
            throw HttpExceptionFactory::create(
                500,
                'Failed to decode JSON response: ' . $e->getMessage(),
                null
            );
        } catch (Exception $e) {
            if ($e instanceof PsnApiException) {
                throw $e;
            }

            throw HttpExceptionFactory::create(
                500,
                'Unexpected error: ' . $e->getMessage(),
                null
            );
        }
    }

    /**
     * Get product by ID
     *
     * @throws PsnApiException
     */
    public function getProductById(RequestProductById $request): Product
    {
        /** @var Product */
        return $this->execute($request);
    }

    /**
     * Get product list (catalog)
     *
     * @throws PsnApiException
     */
    public function getProductList(RequestProductList $request): CatalogResponseDataCategoryGridRetrieve
    {
        /** @var CatalogResponseDataCategoryGridRetrieve */
        return $this->execute($request);
    }

    /**
     * Get concept by ID
     *
     * @throws PsnApiException
     */
    public function getConceptById(RequestConceptById $request): Concept
    {
        /** @var Concept */
        return $this->execute($request);
    }

    /**
     * Get add-ons by title ID
     *
     * @throws PsnApiException
     */
    public function getAddOnsByTitleId(RequestAddOnsByTitleId $request): AddOnsResponseDataAddOnProductsByTitleIdRetrieve
    {
        /** @var AddOnsResponseDataAddOnProductsByTitleIdRetrieve */
        return $this->execute($request);
    }

    /**
     * Get PS Plus tier offers
     *
     * @throws PsnApiException
     */
    public function getPSPlusTier(RequestPSPlusTier $request): PSPlusOffersResponseDataTierSelectorOffersRetrieve
    {
        /** @var PSPlusOffersResponseDataTierSelectorOffersRetrieve */
        return $this->execute($request);
    }

    /**
     * Get concept by product ID
     *
     * @throws PsnApiException
     */
    public function getConceptByProductId(RequestConceptByProductId $request): Concept
    {
        /** @var Concept */
        return $this->execute($request);
    }

    /**
     * Get concept star rating
     *
     * @throws PsnApiException
     */
    public function getConceptStarRating(RequestConceptStarRating $request): Concept
    {
        /** @var Concept */
        return $this->execute($request);
    }

    /**
     * Get pricing data by concept ID
     *
     * @throws PsnApiException
     */
    public function getPricingDataByConceptId(RequestPricingDataByConceptId $request): Concept
    {
        /** @var Concept */
        return $this->execute($request);
    }

    /**
     * Get product star rating
     *
     * @throws PsnApiException
     */
    public function getProductStarRating(RequestProductStarRating $request): Product
    {
        /** @var Product */
        return $this->execute($request);
    }

    /**
     * Get HTTP response for a request
     *
     * @throws PsnApiException
     */
    private function getResponse(BaseRequest $request): ResponseInterface
    {
        try {
            // Check for hash override
            $operationName = $request->getOperationName();
            $sha256Hash = $this->hashOverrides[$operationName] ?? $request->getSha256Hash();

            $queryString = http_build_query([
                'operationName' => $operationName,
                'variables' => json_encode($request, JSON_THROW_ON_ERROR),
                'extensions' => json_encode([
                    'persistedQuery' => [
                        'version' => 1,
                        'sha256Hash' => $sha256Hash,
                    ],
                ], JSON_THROW_ON_ERROR),
            ]);

            $uri = $this->baseUri . 'op?' . $queryString;

            $httpRequest = $this->requestFactory->createRequest('GET', $uri)
                ->withHeader('x-psn-store-locale-override', $this->regionEnum->value)
                ->withHeader('content-type', self::HEADER_CONTENT_TYPE);

            $response = $this->httpClient->sendRequest($httpRequest);

            // Handle HTTP errors
            if ($response->getStatusCode() >= 400) {
                $responseData = $this->decodeResponse($response);
                $message = is_string($responseData['message'] ?? null)
                    ? $responseData['message']
                    : $response->getReasonPhrase();

                throw HttpExceptionFactory::create(
                    $response->getStatusCode(),
                    $message,
                    $responseData
                );
            }

            return $response;

        } catch (PsnApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(
                500,
                'Request failed: ' . $e->getMessage(),
                null
            );
        }
    }

    /**
     * Decode JSON response
     *
     * @return array<string, mixed>
     * @throws JsonException
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();

        /** @var array<string, mixed> */
        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Deserialize response data to DTO
     *
     * @param array<string, mixed> $data
     * @param string $dataPath Path to data in response (e.g., 'data.productRetrieve')
     */
    private function deserializeResponse(array $data, string $dtoClass, string $dataPath): mixed
    {
        return $this->serializer->deserialize(
            json_encode($data),
            $dtoClass,
            'json',
            ['dataPath' => $dataPath]
        );
    }

    /**
     * Handle API errors from response
     *
     * @param array<int, array<string, mixed>> $errors
     * @throws PsnApiException
     */
    private function handleApiErrors(array $errors, int $statusCode): void
    {
        $errorMessage = 'API Error';
        if (count($errors) > 0 && isset($errors[0]['message']) && is_string($errors[0]['message'])) {
            $errorMessage = $errors[0]['message'];
        }

        throw HttpExceptionFactory::create($statusCode, $errorMessage, ['errors' => $errors]);
    }

}
