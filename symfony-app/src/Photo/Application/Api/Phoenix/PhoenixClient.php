<?php

declare(strict_types=1);

namespace App\Photo\Application\Api\Phoenix;

use App\Photo\Application\Api\Phoenix\Response\PhotosCollection;

interface PhoenixClient
{
    public function getPhotos(string $accessToken): PhotosCollection;
}
