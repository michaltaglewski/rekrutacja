<?php

declare(strict_types=1);

namespace App\User\Ui\Http\Controller;

use App\User\Domain\Entity\PhoenixAccessToken;
use App\User\Domain\Repository\PhoenixAccessTokenRepository;
use App\User\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PhoenixAccessTokenRepository $phoenixAccessTokenRepository,
    ) {
    }

    #[Route('/profile', name: 'profile')]
    public function profile(Request $request): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        $user = $this->userRepository->findByUserId($userId);

        if (!$user) {
            $session->clear();
            return $this->redirectToRoute('home');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/phoenix', name: 'profile_phoenix', methods: ['GET'])]
    public function phoenix(Request $request): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        $user = $this->userRepository->findByUserId($userId);
        $accessToken = $this->phoenixAccessTokenRepository->getByUserId($userId);

        if (!$user) {
            $session->clear();
            return $this->redirectToRoute('home');
        }

        return $this->render('profile/phoenix.html.twig', [
            'user' => $user,
            'accessToken' => $accessToken?->getAccessToken()
        ]);
    }

    #[Route('/profile/phoenix', name: 'post_profile_phoenix', methods: ['POST'])]
    public function updatePhoenix(Request $request): Response
    {
        // @TODO request validation
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        $accessToken = $request->get('phoenix_access_token');

        $phoenixAccessToken = $this->phoenixAccessTokenRepository->getByUserId($userId);

        if (!$phoenixAccessToken) {
            $phoenixAccessToken = new PhoenixAccessToken(null, $userId, $accessToken);
        } else {
            $phoenixAccessToken->setAccessToken($accessToken);
        }

        $this->phoenixAccessTokenRepository->save($phoenixAccessToken);

        $this->addFlash('success', 'Phoenix access token updated successfully.');

        return $this->redirectToRoute('profile_phoenix');
    }
}
