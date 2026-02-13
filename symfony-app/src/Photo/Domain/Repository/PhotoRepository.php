<?php

declare(strict_types=1);

namespace App\Photo\Domain\Repository;

use App\Photo\Domain\Entity\Photo;

interface PhotoRepository
{
    public function findByIdWithLikes(int $id): ?Photo;

    public function save(Photo $photo): void;

    public function updatePhotoWithLikes(Photo $photo): void;

    public function setLikeCounter(Photo $photo): void;
}
