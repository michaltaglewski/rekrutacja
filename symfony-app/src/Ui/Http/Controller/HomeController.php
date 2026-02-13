<?php

declare(strict_types=1);

namespace App\Ui\Http\Controller;

use App\Photo\Domain\Repository\LikeRepository;
use App\Photo\Domain\Repository\PhotoRepository;
use App\User\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly LikeRepository $likeRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $filters = [
            'location' => $request->query->get('location', ''),
            'camera' => $request->query->get('camera', ''),
            'description' => $request->query->get('description', ''),
            'taken_at' => $request->query->get('taken_at', ''),
            'username' => $request->query->get('username', ''),
        ];

        $filters = array_filter($filters, fn ($value) => $value !== '');

        $photos = $this->photoRepository->findAllWithUsers($filters);

        $session = $request->getSession();
        $userId = $session->get('user_id');
        $currentUser = null;
        $userLikes = [];

        if ($userId) {
            $currentUser = $this->userRepository->findByUserId($userId);

            if ($currentUser) {
                foreach ($photos as $photo) {
                    $this->likeRepository->setUserId($currentUser->getId());
                    $userLikes[$photo->getId()] = $this->likeRepository->hasUserLikedPhoto($photo);
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'currentUser' => $currentUser,
            'userLikes' => $userLikes,
        ]);
    }
}
