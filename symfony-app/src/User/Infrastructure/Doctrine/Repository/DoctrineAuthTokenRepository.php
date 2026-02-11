<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Repository;

use App\User\Domain\Entity\AuthToken;
use App\User\Domain\Repository\AuthTokenRepository;
use App\Entity\AuthToken as AuthTokenEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineAuthTokenRepository extends ServiceEntityRepository implements AuthTokenRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthTokenEntity::class);
    }

    public function getByToken(string $token): ?AuthToken
    {
        $entityManager = $this->getEntityManager();

        $connection = $entityManager->getConnection();

        $sql = "SELECT * FROM auth_tokens WHERE token = '$token'";
        $result = $connection->executeQuery($sql);
        $tokenData = $result->fetchAssociative();
        if (!$tokenData) {
            return null;
        }

        return new AuthToken(
            $tokenData['id'],
            $tokenData['token']
        );
    }
}
