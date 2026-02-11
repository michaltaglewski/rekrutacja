<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Doctrine\Entity\User as UserEntity;
use App\User\Infrastructure\Doctrine\Mapper\UserMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    public function findByUserId(int $userId): ?User
    {
        $entityManager = $this->getEntityManager();

        $user = $entityManager->getRepository(UserEntity::class)->find($userId);

        return $user ? UserMapper::toDomain($user) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $entityManager = $this->getEntityManager();

        $connection = $entityManager->getConnection();

        $userSql = "SELECT * FROM users WHERE username = '$username'";
        $userResult = $connection->executeQuery($userSql);
        $user = $userResult->fetchAssociative();

        if (!$user) {
            return null;
        }

        // @TODO better mapping with associative
        return new User(
            $user['id'],
            $user['username'],
            $user['email'],
            $user['name'],
            $user['last_name'],
            $user['age'],
            $user['bio']
        );
    }
}
