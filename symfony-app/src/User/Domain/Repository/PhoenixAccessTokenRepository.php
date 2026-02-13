<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\PhoenixAccessToken;

interface PhoenixAccessTokenRepository
{
    public function getByUserId(int $userId): ?PhoenixAccessToken;

    public function save(PhoenixAccessToken $accessToken): void;
}
