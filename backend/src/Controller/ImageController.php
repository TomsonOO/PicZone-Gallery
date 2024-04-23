<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Aws\S3\S3Client;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class ImageController extends AbstractController
{

    #[Route('/images/presigned-url/{objectKey}', name: 'image_presigned_url')]
    #[OA\Get(
        path: "/images/presigned-url/{objectKey}",
        operationId: "getPresignedUrl",
        description: "Generates a presigned URL for directly accessing an image from AWS S3 without needing AWS credentials.",
        summary: "Get AWS S3 presigned URL",
        tags: ["Image"],
        parameters: [
            new OA\Parameter(
                name: "objectKey",
                description: "The S3 object key for the image",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Presigned URL returned successfully",
                content: new OA\JsonContent(
                    type: "string",
                    example: "https://examplebucket.s3.amazonaws.com/example.jpg?AWSAccessKeyId=..."
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error generating presigned URL",
                content: new OA\JsonContent(
                    type: "string",
                    example: "Error generating presigned URL"
                )
            ),
        ]
    )] public function getPresignedUrl(string $objectKey): Response
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
    #[OA\Get(
        path: "/api/images",
        operationId: "listImages",
        description: "Retrieves a list of images that are marked to be shown on the homepage.",
        summary: "List images",
        tags: ["Image"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of images",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Image::class)),

                ),
            )
        ]
    )]
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
