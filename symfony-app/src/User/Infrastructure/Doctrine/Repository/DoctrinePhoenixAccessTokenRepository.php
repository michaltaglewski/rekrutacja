<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Repository;

use App\User\Domain\Entity\PhoenixAccessToken;
use App\User\Domain\Repository\PhoenixAccessTokenRepository;
use App\User\Infrastructure\Doctrine\Entity\PhoenixAccessToken as PhoenixAccessTokenEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrinePhoenixAccessTokenRepository extends ServiceEntityRepository implements PhoenixAccessTokenRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoenixAccessTokenEntity::class);
    }

    public function getByUserId(int $userId): ?PhoenixAccessToken
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $sql = "SELECT * FROM phoenix_access_tokens WHERE user_id = :user_id";
        $result = $connection->executeQuery($sql, ['user_id' => $userId]);
        $tokenData = $result->fetchAssociative();

        if (!$tokenData) {
            return null;
        }

        return new PhoenixAccessToken(
            $tokenData['id'],
            $tokenData['user_id'],
            $tokenData['access_token']
        );
    }

    public function save(PhoenixAccessToken $accessToken): void
    {
        $entityManager = $this->getEntityManager();

        $photoEntity = $accessToken->getId() ? $entityManager->find(
            PhoenixAccessTokenEntity::class,
            $accessToken->getId()
        ) : new PhoenixAccessTokenEntity();

        $photoEntity->setUserId($accessToken->getUserId());
        $photoEntity->setAccessToken($accessToken->getAccessToken());

        $entityManager->persist($photoEntity);
        $entityManager->flush();
    }
}
