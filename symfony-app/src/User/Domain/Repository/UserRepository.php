<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\User;

interface UserRepository
{
    public function findByUsername(string $username): ?User;
}
