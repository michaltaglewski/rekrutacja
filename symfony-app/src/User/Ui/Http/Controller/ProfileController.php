<?php

declare(strict_types=1);

namespace App\User\Ui\Http\Controller;

use App\Photo\Application\Api\Phoenix\PhoenixClient;
use App\Photo\Application\Exception\PhoenixApiClientHttpException;
use App\Photo\Application\Exception\PhoenixApiUnauthorizedHttpException;
use App\Photo\Domain\Entity\Photo;
use App\Photo\Domain\Repository\PhotoRepository;
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
        private readonly PhotoRepository $photoRepository,
        private readonly PhoenixAccessTokenRepository $phoenixAccessTokenRepository,
        private readonly PhoenixClient $phoenixClient,
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

    #[Route('/profile/import-photos', name: 'profile_import_photos')]
    public function importPhotos(Request $request): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            $this->addFlash('error', 'You must be logged in to import photos.');
            return $this->redirectToRoute('home');
        }

        $phoenixAccessToken = $this->phoenixAccessTokenRepository->getByUserId($userId);
        if (!$phoenixAccessToken) {
            $this->addFlash('error', 'You must first configure your Phoenix access token.');
            return $this->redirectToRoute('profile');
        }

        try {
            /**
             * @TODO still needs some refinement - better validation like user ID verification,
             * avoiding multiple imports of the same photos
             */
            $collection = $this->phoenixClient->getPhotos(
                $phoenixAccessToken->getAccessToken()
            );

            foreach ($collection->getPhotos() as $photoData) {
                $photo = new Photo(null, $userId, $photoData['photo_url']);
                $this->photoRepository->save($photo);
            }

            $this->addFlash('success', 'Photos imported successfully.');
        } catch (PhoenixApiUnauthorizedHttpException) {
            $this->addFlash('error', 'Unauthorized request: Please check your Phoenix access token.');
            return $this->redirectToRoute('profile');
        } catch (PhoenixApiClientHttpException) {
            $this->addFlash('error', 'Internal error: Could not fetch photos from Phoenix.');
            return $this->redirectToRoute('profile');
        }

        return $this->redirectToRoute('home');
    }
}
