<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use Aws\S3\S3Client;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/images')]
class ImageController extends AbstractController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    #[Route('/presigned-url/{objectKey}', name: 'image_presigned_url', requirements: ['objectKey' => '.+'])]
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

    #[Route('', name: 'api_images', methods: ['GET'])]
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
    #[Route('/upload', name: 'image_upload', methods: ['POST'])]
    #[OA\Post(
        path: "/images/upload",
        operationId: "uploadImage",
        description: "Uploads an image to AWS S3 and stores its metadata in the database.",
        summary: "Upload Image",
        requestBody: new OA\RequestBody(
            description: "Image file and metadata to upload",
            required: true,
            content: new OA\JsonContent(
                required: ["image", "description", "type", "showOnHomePage"],
                properties: [
                    new OA\Property(
                        property: "image",
                        description: "The image file to upload",
                        type: "string",
                        format: "binary"
                    ),
                    new OA\Property(
                        property: "description",
                        description: "Description of the image",
                        type: "string"
                    ),
                    new OA\Property(
                        property: "type",
                        description: "Type of the image (gallery or profile)",
                        type: "string"
                    ),
                    new OA\Property(
                        property: "showOnHomePage",
                        description: "Flag to indicate if the image should be shown on the homepage",
                        type: "boolean"
                    )
                ],
                type: "object"
            )
        ),
        tags: ["Image"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Image uploaded successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Image uploaded successfully"
                        ),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 123),
                                new OA\Property(property: "filename", type: "string", example: "example.jpg"),
                                new OA\Property(property: "url", type: "string", example: "https://examplebucket.s3.amazonaws.com/example.jpg"),
                                new OA\Property(property: "description", type: "string", example: "A description of the image"),
                                new OA\Property(property: "type", type: "string", example: "gallery"),
                                new OA\Property(property: "showOnHomepage", type: "boolean", example: true)
                            ],
                            type: "object"
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid file upload",
                content: new OA\JsonContent(
                    type: "string",
                    example: "Invalid file upload"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error processing the image",
                content: new OA\JsonContent(
                    type: "string",
                    example: "Error processing the image"
                )
            )
        ]
    )]    public function uploadImage(Request $request): JsonResponse
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
    #[OA\Delete(
        path: "/images/{id}",
        operationId: "deleteImage",
        description: "Deletes an image from AWS S3 and removes its metadata from the database.",
        summary: "Delete Image",
        tags: ["Image"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "The unique identifier of the image to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer",
                    example: 123
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Image deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Image deleted successfully"
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Image not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Image not found"
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error deleting the image",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Error deleting the image"
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
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
