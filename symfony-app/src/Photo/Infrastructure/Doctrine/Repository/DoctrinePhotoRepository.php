<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine\Repository;

use App\Photo\Domain\Entity\Photo;
use App\Photo\Domain\Repository\PhotoRepository;
use App\Photo\Infrastructure\Doctrine\Entity\Photo as PhotoEntity;
use App\Photo\Infrastructure\Doctrine\Mapper\PhotoMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrinePhotoRepository extends ServiceEntityRepository implements PhotoRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoEntity::class);
    }

    public function findAllWithUsers(?array $filters = []): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        if (!empty($filters['location'])) {
            $queryBuilder->andWhere('p.location LIKE :location')
                ->setParameter('location', '%' . $filters['location'] . '%');
        }

        if (!empty($filters['camera'])) {
            $queryBuilder->andWhere('p.camera LIKE :camera')
                ->setParameter('camera', '%' . $filters['camera'] . '%');
        }

        if (!empty($filters['description'])) {
            $queryBuilder->andWhere('p.description LIKE :description')
                ->setParameter('description', '%' . $filters['description'] . '%');
        }

        if (!empty($filters['taken_at'])) {
            $queryBuilder->andWhere('DATE(p.takenAt) = :taken_at')
                ->setParameter('taken_at', $filters['taken_at']);
        }

        if (!empty($filters['username'])) {
            $queryBuilder->andWhere('u.username LIKE :username')
                ->setParameter('username', '%' . $filters['username'] . '%');
        }

        return $queryBuilder->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByIdWithLikes(int $id): ?Photo
    {
        $entityManager = $this->getEntityManager();

        $entity = $entityManager->find(
            PhotoEntity::class,
            $id
        );

        if (!$entity) {
            return null;
        }

        return PhotoMapper::toDomain($entity);
    }

    public function save(Photo $photo): void
    {
        if ($photo->getId() === null) {
            $this->insert($photo);

            return;
        }

        $this->update($photo);
    }

    public function updatePhotoWithLikes(Photo $photo): void
    {
        $entityManager = $this->getEntityManager();

        $photoEntity = $entityManager->find(
            PhotoEntity::class,
            $photo->getId()
        );

        if (!$photoEntity) {
            throw new \RuntimeException('Photo not found');
        }

        $photoEntity = PhotoMapper::setEntityWithLikes($photo, $photoEntity, $entityManager);

        $entityManager->persist($photoEntity);
        $entityManager->flush();
    }

    public function setLikeCounter(Photo $photo): void
    {
        $entityManager = $this->getEntityManager();

        $photoEntity = $entityManager->find(
            PhotoEntity::class,
            $photo->getId()
        );

        $photoEntity->setLikeCounter($photo->getLikesCount());

        $entityManager->persist($photoEntity);
        $entityManager->flush();
    }

    private function insert(Photo $photo): void
    {
        $entityManager = $this->getEntityManager();

        $photoEntity = new PhotoEntity();
        $photoEntity = PhotoMapper::toEntity($photo, $photoEntity, $entityManager);

        $entityManager->persist($photoEntity);
        $entityManager->flush();
    }

    private function update(Photo $photo): void
    {
        $entityManager = $this->getEntityManager();

        $photoEntity = $entityManager->find(
            PhotoEntity::class,
            $photo->getId()
        );

        if (!$photoEntity) {
            throw new \RuntimeException('Photo not found');
        }

        $photoEntity = PhotoMapper::toEntity($photo, $photoEntity, $entityManager);

        $entityManager->persist($photoEntity);
        $entityManager->flush();
    }
}
