<?php

declare(strict_types=1);

namespace PlaystationStoreApi\Dto\Product;

use PlaystationStoreApi\Dto\Common\Description;
use PlaystationStoreApi\Dto\Common\LocalizedGenre;
use PlaystationStoreApi\Dto\Common\Media;
use PlaystationStoreApi\Dto\Common\Price;
use PlaystationStoreApi\Dto\Concept\Concept;

/**
 * Product information from PlayStation Store
 */
final readonly class Product
{
    /**
     * @param string[]|null $platforms
     * @param string[]|null $screenLanguages
     * @param string[]|null $spokenLanguages
     * @param Media[]|null $media
     * @param LocalizedGenre[]|null $combinedLocalizedGenres
     * @param Description[]|null $descriptions
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $invariantName = null,
        public ?string $npTitleId = null, // Added: CUSA ID
        /** @var string[]|null */
        public ?array $platforms = null,
        public ?string $publisherName = null,
        public ?\DateTimeInterface $releaseDate = null,
        public ?string $storeDisplayClassification = null, // e.g. "FULL_GAME"
        public ?string $localizedStoreDisplayClassification = null, // e.g. "Full Game"
        public ?Price $price = null,
        /** @var Media[]|null */
        public ?array $media = null,
        public ?Concept $concept = null, // Added: Link to parent concept
        public ?ContentRating $contentRating = null, // Added: ESRB/PEGI
        public ?Edition $edition = null, // Added: Edition info
        /** @var LocalizedGenre[]|null */
        public ?array $combinedLocalizedGenres = null, // Added: Genres
        /** @var Description[]|null */
        public ?array $descriptions = null, // Added: Long description, legal, etc
        /** @var string[]|null */
        public ?array $screenLanguages = null,
        /** @var string[]|null */
        public ?array $spokenLanguages = null,
    ) {
    }
}
