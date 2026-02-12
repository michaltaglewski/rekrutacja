<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine\Repository;

use App\Photo\Domain\Repository\LikeRepository;
use App\Photo\Infrastructure\Doctrine\Entity\Like;
use App\Photo\Infrastructure\Doctrine\Entity\Photo;
use App\User\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineLikeRepository extends ServiceEntityRepository implements LikeRepository
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
