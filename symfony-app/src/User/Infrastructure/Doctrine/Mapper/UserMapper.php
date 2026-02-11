<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Mapper;

use App\Entity\User as UserEntity;
use App\User\Domain\Entity\User;

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
