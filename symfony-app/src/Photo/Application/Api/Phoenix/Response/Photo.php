<?php

declare(strict_types=1);

namespace App\Photo\Application\Api\Phoenix\Response;

class Photo
{
    public function __construct(
        private readonly int $id,
        private readonly string $photoUrl
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }
}
