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
use PlaystationStoreApi\Request\RequestCatalog;
use PlaystationStoreApi\Request\RequestConceptById;
use PlaystationStoreApi\Request\RequestConceptByProductId;
use PlaystationStoreApi\Request\RequestConceptStarRating;
use PlaystationStoreApi\Request\RequestPricingDataByConceptId;
use PlaystationStoreApi\Request\RequestProductById;
use PlaystationStoreApi\Request\RequestProductStarRating;
use PlaystationStoreApi\Request\RequestPSPlusTier;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class Client implements PlaystationStoreClientInterface
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
        private readonly string $baseUri = self::DEFAULT_BASE_URI,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Get logger instance (returns NullLogger if none provided)
     */
    private function getLogger(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
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
        $logger = $this->getLogger();

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

            // Denormalize directly from array to target DTO with dataPath in context
            // Serializer implements DenormalizerInterface, so we can use denormalize directly
            if (! $this->serializer instanceof DenormalizerInterface) {
                throw HttpExceptionFactory::create(
                    500,
                    'Serializer must implement DenormalizerInterface to denormalize responses',
                    null
                );
            }

            try {
                $responseDto = $this->serializer->denormalize(
                    $responseData, // Already an array, no need to encode/decode
                    $dtoClass,
                    'json',
                    ['dataPath' => $dataPath]
                );
            } catch (\Throwable $e) {
                $logger->error('Denormalization error', [
                    'operation' => $request->getOperationName(),
                    'dto_class' => $dtoClass,
                    'data_path' => $dataPath,
                    'error' => $e->getMessage(),
                    'response_data' => $responseData,
                ]);

                throw $e;
            }

            if (! is_object($responseDto)) {
                $logger->error('Denormalization returned non-object', [
                    'operation' => $request->getOperationName(),
                    'dto_class' => $dtoClass,
                    'data_path' => $dataPath,
                    'response_data' => $responseData,
                ]);

                throw HttpExceptionFactory::create(
                    500,
                    'Failed to denormalize response to object',
                    null
                );
            }

            return $responseDto;

        } catch (JsonException $e) {
            $logger->error('JSON decode error', [
                'operation' => $request->getOperationName(),
                'error' => $e->getMessage(),
            ]);

            throw HttpExceptionFactory::create(
                500,
                'Failed to decode JSON response: ' . $e->getMessage(),
                null
            );
        } catch (Exception $e) {
            if ($e instanceof PsnApiException) {
                throw $e;
            }

            $logger->error('Unexpected error during request execution', [
                'operation' => $request->getOperationName(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

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
     * Get catalog (list of concepts)
     *
     * Note: API returns concepts, not products directly.
     * Each concept contains a products array with related products.
     * Use CatalogResponseDataCategoryGridRetrieve::getAllProducts() to extract all products from concepts.
     *
     * @throws PsnApiException
     */
    public function getCatalog(RequestCatalog $request): CatalogResponseDataCategoryGridRetrieve
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

            // Log hash being used
            $logger = $this->getLogger();
            if (isset($this->hashOverrides[$operationName])) {
                $logger->info('Using overridden SHA-256 hash for operation', [
                    'operation' => $operationName,
                    'hash' => $sha256Hash,
                ]);
            } else {
                $logger->debug('Using default SHA-256 hash for operation', [
                    'operation' => $operationName,
                    'hash' => $sha256Hash,
                ]);
            }

            // Normalize request object to array using Symfony Serializer for reliability
            // This ensures consistent serialization even if private properties or getters are added
            // Service methods from BaseRequest interface are marked with #[Ignore] attribute to exclude them from normalization
            if (! $this->serializer instanceof NormalizerInterface) {
                throw HttpExceptionFactory::create(
                    500,
                    'Serializer must implement NormalizerInterface to normalize requests',
                    null
                );
            }

            /** @var NormalizerInterface $normalizer */
            $normalizer = $this->serializer;
            $normalizedRequest = $normalizer->normalize($request);
            if (! is_array($normalizedRequest)) {
                throw HttpExceptionFactory::create(
                    500,
                    'Failed to normalize request to array',
                    null
                );
            }

            // Remove BaseRequest interface metadata from variables (these are not actual request parameters)
            unset(
                $normalizedRequest['responseDtoClass'],
                $normalizedRequest['operationName'],
                $normalizedRequest['sha256Hash'],
                $normalizedRequest['dataPath']
            );

            $queryString = http_build_query([
                'operationName' => $operationName,
                'variables' => json_encode($normalizedRequest, JSON_THROW_ON_ERROR),
                'extensions' => json_encode([
                    'persistedQuery' => [
                        'version' => 1,
                        'sha256Hash' => $sha256Hash,
                    ],
                ], JSON_THROW_ON_ERROR),
            ]);

            $uri = $this->baseUri . 'op?' . $queryString;

            // Log outgoing request (INFO level - basic info only)
            $logger->info('Sending request to PlayStation Store API', [
                'operation' => $operationName,
                'uri' => $uri,
                'region' => $this->regionEnum->value,
            ]);

            // Log detailed request variables (DEBUG level - full data)
            $logger->debug('Request variables', [
                'operation' => $operationName,
                'variables' => $normalizedRequest,
            ]);

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
