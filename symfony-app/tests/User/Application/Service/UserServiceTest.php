<?php

declare(strict_types=1);

namespace User\Application\Service;

use App\User\Application\Exception\UnauthorizedException;
use App\User\Application\Exception\UserNotFoundException;
use App\User\Application\Service\UserService;
use App\User\Domain\Entity\AuthToken;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\AuthTokenRepository;
use App\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    private AuthTokenRepository|MockObject $authTokenRepository;
    private AuthTokenRepository|MockObject $userRepository;
    private UserService $userServiceUnderTest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authTokenRepository = $this->createMock(AuthTokenRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->userServiceUnderTest = new UserService(
            $this->authTokenRepository,
            $this->userRepository,
        );
    }

    public function testGetAuthUserFailureWhenAuthTokenNotExists(): void
    {
        // Expect
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Invalid token');

        // Given
        $username = 'test_user';
        $token = 'test_token';

        // When
        $this->authTokenRepository
            ->expects(self::once())
            ->method('getByToken')
            ->willReturn(null);

        $this->userRepository
            ->expects(self::never())
            ->method('findByUsername');

        // Then
        $this->userServiceUnderTest->getAuthUser($username, $token);
    }

    public function testGetAuthUserFailureWhenUserNotExists(): void
    {
        // Expect
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Given
        $username = 'test_user';
        $token = 'test_token';

        $authToken = new AuthToken(1, $token);

        // When
        $this->authTokenRepository
            ->expects(self::once())
            ->method('getByToken')
            ->willReturn($authToken);

        $this->userRepository
            ->expects(self::once())
            ->method('findByUsername')
            ->willReturn(null);

        // Then
        $this->userServiceUnderTest->getAuthUser($username, $token);
    }

    public function testGetAuthUserFailureWhenAuthTokenDoesNotBelongForTheUser(): void
    {
        // Expect
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Given
        $username = 'test_user';
        $token = 'test_token';

        $authToken = new AuthToken(1, $token);
        $user = new User(
            2,
            $token,
            'test@example.com',
        );

        // When
        $this->authTokenRepository
            ->expects(self::once())
            ->method('getByToken')
            ->willReturn($authToken);

        $this->userRepository
            ->expects(self::once())
            ->method('findByUsername')
            ->willReturn($user);

        // Then
        $this->userServiceUnderTest->getAuthUser($username, $token);
    }

    public function testGetAuthUserSuccess(): void
    {
        // Given
        $username = 'test_user';
        $token = 'test_token';

        $authToken = new AuthToken(1, $token);
        $user = new User(
            1,
            $token,
            'test@example.com',
        );

        // When
        $this->authTokenRepository
            ->expects(self::once())
            ->method('getByToken')
            ->willReturn($authToken);

        $this->userRepository
            ->expects(self::once())
            ->method('findByUsername')
            ->willReturn($user);

        // Then
        $actual = $this->userServiceUnderTest->getAuthUser($username, $token);

        $this->assertNotNull($actual);
    }
}
