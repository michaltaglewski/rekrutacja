<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine\Repository;

use App\Photo\Domain\Repository\LikeRepository;
use App\Photo\Infrastructure\Doctrine\Entity\Like;
use App\Photo\Infrastructure\Doctrine\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineLikeRepository extends ServiceEntityRepository implements LikeRepository
{
    private int $userId;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    #[\Override]
    public function hasUserLikedPhoto(Photo $photo): bool
    {
        $likes = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.userId = :user_id')
            ->andWhere('l.photo = :photo')
            ->setParameter('user_id', $this->userId)
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getArrayResult();

        return count($likes) > 0;
    }
}
