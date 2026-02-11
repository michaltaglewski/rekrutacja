<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

final class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $username,
        private readonly string $email,
        private readonly ?string $name,
        private readonly ?string $lastName,
        private readonly ?int $age,
        private readonly ?string $bio,
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }
}
