<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine\Mapper;

use App\Photo\Domain\Entity\Like;
use App\Photo\Domain\Entity\Photo;
use App\Photo\Infrastructure\Doctrine\Entity\Like as LikeEntity;
use App\Photo\Infrastructure\Doctrine\Entity\Photo as PhotoEntity;
use App\User\Infrastructure\Doctrine\Entity\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;

class PhotoMapper
{
    public static function toDomain(PhotoEntity $entity): Photo
    {
        $likes = [];

        foreach ($entity->likes as $likeEntity) {
            $likes[] = new Like(
                $likeEntity->getUserId(),
                $entity->getId()
            );
        }

        $photo = new Photo(
            $entity->getId(),
            $entity->getUser()->getId()
        );

        $photo->setLikes($likes);

        return $photo;
    }

    public static function setEntityWithLikes(
        Photo $photo,
        PhotoEntity $photoEntity,
        EntityManagerInterface $entityManager
    ): PhotoEntity {
        $userIds = array_map(fn($like) => $like->getUserId(), $photo->getLikes());

        // Remove likes that are not in the new list
        foreach ($photoEntity->likes->toArray() as $existingLike) {
            if (!in_array($existingLike->getUserId(), $userIds, true)) {
                $photoEntity->likes->removeElement($existingLike);
                $entityManager->remove($existingLike);
            }
        }

        $existingUserIds = array_map(fn($like) => $like->getUserId(), $photoEntity->likes->toArray());

        foreach ($photo->getLikes() as $like) {
            if (!in_array($like->getUserId(), $existingUserIds, true)) {
                $likeEntity = new LikeEntity();
                $likeEntity->setPhoto($photoEntity);
                $likeEntity->setUserId($like->getUserId());

                $userEntity = $entityManager->getReference(
                    UserEntity::class,
                    $like->getUserId()
                );

                $likeEntity->setUser($userEntity);

                $photoEntity->likes->add($likeEntity);
            }
        }

        return $photoEntity;
    }
}
