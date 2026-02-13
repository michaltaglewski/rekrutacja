<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Api\Phoenix;

class UriResolver
{
    public function __construct(
        private readonly string $baseUrl
    ) {
    }

    public function getUri(string $path): string
    {
        return $this->baseUrl . $path;
    }
}
