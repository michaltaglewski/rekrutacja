<?php

declare(strict_types=1);

namespace App\Likes;

use App\Entity\Photo;
use App\User\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LikeRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    private ?User $user;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    #[\Override]
    public function hasUserLikedPhoto(Photo $photo): bool
    {
        $likes = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $this->user)
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getArrayResult();

        return count($likes) > 0;
    }
}
