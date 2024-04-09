<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{

    #[Route('/images/presigned-url/{objectKey}', name: 'image_presigned_url')]
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

    #[Route('/api/images', name: 'api_images', methods: ['GET'])]
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


}
