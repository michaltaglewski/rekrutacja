<?php

declare(strict_types=1);

namespace App\Photo\Domain\Entity;

use InvalidArgumentException;

final class Photo
{
    /** @var Like[] */
    private array $likes = [];

    public function __construct(
        private readonly int $id,
        private readonly int $userId
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setLikes(array $likes): void
    {
        foreach ($likes as $like) {
            if (!$like instanceof Like) {
                throw new InvalidArgumentException('All items must be instances of Like');
            }

            if ($like->getPhotoId() !== $this->id) {
                throw new InvalidArgumentException(
                    sprintf('Like with photoId %d does not belong to photo %d', $like->getPhotoId(), $this->id)
                );
            }
        }

        $this->likes = $likes;
    }

    public function getLikes(): array
    {
        return $this->likes;
    }

    public function getLikesCount(): int
    {
        return count($this->likes);
    }

    public function like(int $userId): void
    {
        if ($this->isLikedBy($userId)) {
            return;
        }

        $this->likes[] = new Like($userId, $this->id);
    }

    public function unlike(int $userId): void
    {
        $this->likes = array_filter(
            $this->likes,
            fn (Like $like) => !$like->equalsUserId($userId)
        );
    }

    public function isLikedBy(int $userId): bool
    {
        foreach ($this->likes as $like) {
            if ($like->equalsUserId($userId)) {
                return true;
            }
        }

        return false;
    }
}
