<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Service\ImageService;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/images')]
class ImageController extends AbstractController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    #[Route('/presigned-url/{objectKey}', name: 'image_presigned_url', requirements: ['objectKey' => '.+'])]
    public function getPresignedUrl(string $objectKey): Response
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_S3_REGION'],
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);
        try {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $_ENV['AWS_S3_BUCKET_NAME'],
                'Key' => $objectKey,
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
            $presignedUrl = (string)$request->getUri();

            return new Response($presignedUrl);
        } catch (\Exception $e) {
            return new Response('Error generating presigned URL', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
}

    #[Route('', name: 'api_images', methods: ['GET'])]
    public function listImages(ImageRepository $imageRepository): JsonResponse
    {
        $images = $imageRepository->findBy(['showOnHomepage' => true]);

        $data = array_map(function ($image) {
            return [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'url' => $image->getUrl(),
                'objectKey' => $image->getObjectKey(),
            ];
        }, $images);

        return $this->json($data);
    }
    #[Route('/upload', name: 'image_upload', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $file = $request->files->get('image');
        $description = $request->request->get('description', '');
        $imageType = $request->request->get('type', 'gallery');
        $showOnHomepage = ($imageType === 'profile') ? false :
            filter_var($request->request->get('showOnHomePage', 'true'), FILTER_VALIDATE_BOOLEAN);


        if (!$file || !$file->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Invalid file upload.");
        }


        try {
            $image = $this->imageService->uploadImage($file, $description, $imageType, $showOnHomepage);
            return new JsonResponse([
                'message' => 'Image uploaded successfully',
                'data' => [
                    'id' => $image->getId(),
                    'filename' => $image->getFilename(),
                    'url' => $image->getUrl(),
                    'description' => $image->getDescription(),
                    'type' => $image->getType(),
                    'showOnHomepage' => $image->getShowOnHomepage()
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Error processing the image.");
        }
    }

    #[Route('/{id}', name: 'image_delete', methods: ['DELETE'])]
    public function deleteImage(int $id): JsonResponse
    {
        try {
            $this->imageService->deleteImage($id);
            return new JsonResponse(['message' => 'Image deleted successfully'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => 'Image not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error deleting the image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
