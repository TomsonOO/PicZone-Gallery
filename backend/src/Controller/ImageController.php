<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{

    #[Route('/api/images', name: 'api_images', methods: ['GET'])]
    public function listImages(ImageRepository $imageRepository): JsonResponse
    {
        $images = $imageRepository->findBy(['showOnHomepage' => true]);

        $data = array_map(function ($image) {
            return [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'url' => $image->getUrl(),
            ];
        }, $images);

        return $this->json($data);

    }


}
