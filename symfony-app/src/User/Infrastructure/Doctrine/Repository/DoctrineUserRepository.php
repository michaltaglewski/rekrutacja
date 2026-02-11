<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\Entity\User as UserEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    public function findByUsername(string $username): ?User
    {
        $entityManager = $this->getEntityManager();

        $connection = $entityManager->getConnection();

        $userSql = "SELECT * FROM users WHERE username = '$username'";
        $userResult = $connection->executeQuery($userSql);
        $userData = $userResult->fetchAssociative();

        if (!$userData) {
            return null;
        }

        return new User(
            $userData['id'],
            $userData['username']
        );
    }
}
