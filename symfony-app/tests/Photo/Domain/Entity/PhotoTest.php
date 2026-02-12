<?php

declare(strict_types=1);

namespace Photo\Domain\Entity;

use App\Photo\Domain\Entity\Like;
use App\Photo\Domain\Entity\Photo;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhotoTest extends TestCase
{
    public function testGetIdAndGetUserIdSuccess(): void
    {
        // Given
        $photo = new Photo(2, 1);

        // Then
        $this->assertEquals(2, $photo->getId());
        $this->assertEquals(1, $photo->getUserId());
    }

    public function testSetLikesSuccess(): void
    {
        // Given
        $photo = new Photo(2, 1);

        $like1 = new Like(2, 2);
        $like2 = new Like(3, 2);

        // When
        $photo->setLikes([
            $like1, $like2
        ]);

        // Then
        $this->assertCount(2, $photo->getLikes());
    }

    public function testSetLikesFailureWhenOneOfTheLikePhotoIdDoesNotApplyToThePhoto(): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Like with photoId 5 does not belong to photo 2');

        // Given
        $photo = new Photo(2, 1);

        $like1 = new Like(2, 2);
        $like2 = new Like(3, 5);

        // When
        $photo->setLikes([
            $like1, $like2
        ]);
    }

    public function testGetLikeCountSuccess(): void
    {
        // Given
        $photo = new Photo(2, 1);

        $like1 = new Like(2, 2);
        $like2 = new Like(3, 2);
        $like3 = new Like(4, 2);
        $like4 = new Like(5, 2);
        $like5 = new Like(6, 2);

        // When
        $photo->setLikes([
            $like1,
            $like2,
            $like3,
            $like4,
            $like5
        ]);

        // Then
        $this->assertSame(5, $photo->getLikesCount());
    }

    public function testLikeSuccess(): void
    {
        // Given
        $userId = 2;
        $photo = new Photo(2, 1);
        $like = new Like(3, 2);

        // When
        $photo->setLikes([$like]);
        $this->assertCount(1, $photo->getLikes());
        $photo->like($userId);

        // Then
        $this->assertCount(2, $photo->getLikes());
    }

    public function testLikeFailureWhenPhotoIsAlreadyLikedByTheUser(): void
    {
        // Given
        $userId = 2;
        $photo = new Photo(2, 1);
        $like = new Like(2, 2);

        // When
        $photo->setLikes([$like]);
        $this->assertCount(1, $photo->getLikes());
        $photo->like($userId);

        // Then
        $this->assertCount(1, $photo->getLikes());
    }

    public function testUnlikeSuccess(): void
    {
        // Given
        $userId = 2;
        $photo = new Photo(2, 1);
        $like = new Like(2, $userId);

        // When
        $photo->setLikes([$like]);
        $this->assertCount(1, $photo->getLikes());

        $photo->unlike($userId);

        // Then
        $this->assertCount(0, $photo->getLikes());
    }

    public function testIsLikedBySuccessWhenPhotoIsNotLikedByTheUser(): void
    {
        // Given
        $userId = 2;
        $photo = new Photo(2, 1);

        // When
        $actual = $photo->isLikedBy($userId);

        // Then
        $this->assertFalse($actual);
    }

    public function testIsLikedBySuccessWhenPhotoIsLikedByTheUser(): void
    {
        // Given
        $userId = 2;
        $photo = new Photo(2, 1);

        // When
        $photo->setLikes([new Like($userId, 2)]);
        $actual = $photo->isLikedBy($userId);

        // Then
        $this->assertTrue($actual);
    }
}
