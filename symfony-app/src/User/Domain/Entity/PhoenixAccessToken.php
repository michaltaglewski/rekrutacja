<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

final class PhoenixAccessToken
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $userId,
        private string $accessToken,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
