<?php

declare(strict_types=1);

namespace App\Photo\Domain\Entity;

class Like
{
    public function __construct(
        private readonly int $userId,
        private readonly int $photoId,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPhotoId(): int
    {
        return $this->photoId;
    }

    public function equalsUserId(int $userId): bool
    {
        return $this->userId === $userId;
    }
}
