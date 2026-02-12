<?php

declare(strict_types=1);

namespace App\User\Ui\Http\Controller;

use App\User\Application\Exception\UnauthorizedException;
use App\User\Application\Exception\UserNotFoundException;
use App\User\Application\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Route('/auth/{username}/{token}', name: 'auth_login')]
    public function login(Request $request): Response
    {
        try {
            // @TODO request validation
            $username = $request->get('username');
            $token = $request->get('token');

            $user = $this->userService->getAuthUser($username, $token);

            $session = $request->getSession();
            $session->set('user_id', $user->getId());
            $session->set('username', $user->getUsername());

            $this->addFlash('success', 'Welcome back, ' . $username . '!');

            return $this->redirectToRoute('home');
        } catch (UnauthorizedException $exception) {
            return new Response($exception->getMessage(), 401);
        } catch (UserNotFoundException $exception) {
            return new Response($exception->getMessage(), 404);
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        $this->addFlash('info', 'You have been logged out successfully.');

        return $this->redirectToRoute('home');
    }
}
