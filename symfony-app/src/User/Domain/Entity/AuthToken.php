<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

final class AuthToken
{
    public function __construct(
        private readonly int $userId,
        private readonly string $token,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function equalsUserId(int $userId): bool
    {
        return $this->userId === $userId;
    }
}
