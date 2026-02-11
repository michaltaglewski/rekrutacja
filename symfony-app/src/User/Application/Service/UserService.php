<?php

declare(strict_types=1);

namespace App\User\Application\Service;

use App\User\Application\Exception\UnauthorizedException;
use App\User\Application\Exception\UserNotFoundException;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\AuthTokenRepository;
use App\User\Domain\Repository\UserRepository;

class UserService
{
    public function __construct(
        private readonly AuthTokenRepository $authTokenRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     */
    public function getAuthUser(string $username, string $token): User
    {
        $authToken = $this->authTokenRepository->getByToken($token);
        if (!$authToken) {
            throw new UnauthorizedException('Invalid token');
        }

        $user = $this->userRepository->findByUsername($username);
        if (!$user || !$authToken->equalsUserId($user->getId())) {
            throw new UserNotFoundException('User not found');
        }

        return $user;
    }
}
