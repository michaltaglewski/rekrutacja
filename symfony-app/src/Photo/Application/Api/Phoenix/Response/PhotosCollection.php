<?php

declare(strict_types=1);

namespace App\Photo\Application\Api\Phoenix\Response;

class PhotosCollection
{
    /**
     * @param Photo[] $photos
     */
    public function __construct(
        private readonly array $photos
    ) {
    }

    /**
     * @return Photo[]
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }
}
