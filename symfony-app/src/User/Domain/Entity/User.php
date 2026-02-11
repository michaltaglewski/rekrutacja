<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

final class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $username,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
