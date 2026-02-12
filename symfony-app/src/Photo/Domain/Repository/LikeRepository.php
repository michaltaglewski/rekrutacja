<?php

declare(strict_types=1);

namespace App\Photo\Domain\Repository;

use App\Photo\Infrastructure\Doctrine\Entity\Photo;

interface LikeRepository
{
    public function hasUserLikedPhoto(Photo $photo): bool;
}
