<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Mapper;

use App\User\Domain\Entity\User;
use App\User\Infrastructure\Doctrine\Entity\User as UserEntity;

class UserMapper
{
    public static function toDomain(UserEntity $entity): User
    {
        return new User(
            $entity->getId(),
            $entity->getUsername(),
            $entity->getEmail(),
            $entity->getName(),
            $entity->getLastName(),
            $entity->getAge(),
            $entity->getBio(),
        );
    }
}
