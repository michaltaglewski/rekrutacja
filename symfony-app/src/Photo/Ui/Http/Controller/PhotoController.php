<?php

declare(strict_types=1);

namespace App\Photo\Ui\Http\Controller;

use App\Photo\Domain\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly PhotoRepository $photoRepository
    ) {
    }

    #[Route('/photo/{id}/like', name: 'photo_like')]
    public function like(Request $request): Response
    {
        // @TODO request validation
        $id = (int) $request->get('id');

        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            $this->addFlash('error', 'You must be logged in to like photos.');
            return $this->redirectToRoute('home');
        }

        $photo = $this->photoRepository->findByIdWithLikes($id);
        if (!$photo) {
            throw $this->createNotFoundException('Photo not found');
        }

        if ($photo->isLikedBy($userId)) {
            $photo->unlike($userId);
            $this->addFlash('info', 'Photo unliked!');
        } else {
            $photo->like($userId);
            $this->addFlash('success', 'Photo liked!');
        }

        $this->photoRepository->save($photo);
        $this->photoRepository->setLikeCounter($photo);

        return $this->redirectToRoute('home');
    }
}
