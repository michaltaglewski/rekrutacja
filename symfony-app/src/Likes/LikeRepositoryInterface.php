<?php

declare(strict_types=1);

namespace App\Likes;

use App\Entity\Photo;

interface LikeRepositoryInterface
{
    public function hasUserLikedPhoto(Photo $photo): bool;
}
