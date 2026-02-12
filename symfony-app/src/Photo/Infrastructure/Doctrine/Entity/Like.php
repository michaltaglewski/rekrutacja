<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine\Entity;

use App\Photo\Infrastructure\Doctrine\Repository\DoctrineLikeRepository;
use App\User\Infrastructure\Doctrine\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineLikeRepository::class)]
#[ORM\Table(name: 'likes')]
#[ORM\UniqueConstraint(columns: ['photo_id', 'user_id'])]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Photo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Photo $photo;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setPhoto(Photo $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
