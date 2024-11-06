<?php

namespace App\Image\Infrastructure\Web;

use App\Image\Application\DeleteImage\DeleteImageCommand;
use App\Image\Application\DeleteImage\DeleteImageCommandHandler;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQuery;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQueryHandler;
use App\Image\Application\GetProfileImage\GetProfileImageQuery;
use App\Image\Application\GetProfileImage\GetProfileImageQueryHandler;
use App\Image\Application\ListImages\ListImagesQuery;
use App\Image\Application\ListImages\ListImagesQueryHandler;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Domain\Entity\Image;
use App\Shared\Application\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/images')]
class WebImageAdapter extends AbstractController
{
    private ListImagesQueryHandler $listImagesHandler;

    private UploadImageCommandHandler $uploadImageHandler;
    private SerializerInterface $serializer;
    private GetProfileImageQueryHandler $getProfileImageHandler;
    private DeleteImageCommandHandler $deleteImageHandler;
    private GetPresignedUrlQueryHandler $getPresignedUrlHandler;

    public function __construct(
        ListImagesQueryHandler $listImagesHandler,
        SerializerInterface $serializer,
        UploadImageCommandHandler $uploadImageHandler,
        GetProfileImageQueryHandler $getProfileImageHandler,
        DeleteImageCommandHandler $deleteImageHandler,
        GetPresignedUrlQueryHandler $getPresignedUrlHandler
    ){
        $this->listImagesHandler = $listImagesHandler;
        $this->serializer = $serializer;
        $this->uploadImageHandler = $uploadImageHandler;
        $this->getProfileImageHandler = $getProfileImageHandler;
        $this->deleteImageHandler = $deleteImageHandler;
        $this->getPresignedUrlHandler = $getPresignedUrlHandler;
    }

    #[Route('/presigned-url/{objectKey}', name: 'image_presigned_url', requirements: ['objectKey' => '.+'])]
    public function getPresignedUrl(string $objectKey): Response
    {
        $query = new GetPresignedUrlQuery($objectKey);
        $presignedUrl = $this->getPresignedUrlHandler->handle($query);

        return new Response($presignedUrl, Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }

    #[Route('', name: 'api_images', methods: ['GET'])]
    public function listImages(Request $request): JsonResponse
    {
        $query = new ListImagesQuery();
        $images = $this->listImagesHandler->handle($query);

        $data = $this->serializer->serialize($images, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @throws ValidationException
     */
    #[Route('/upload', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $imageFile = $request->files->get('image');
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $description = $request->request->get('description', $originalFilename);
        $showOnHomepage = $request->request->get('showOnHomePage', 'true');
        $imageType = $request->request->get('type', Image::TYPE_GALLERY);

        $command = new UploadImageCommand(
            $originalFilename,
            $showOnHomepage,
            $imageType,
            $imageFile,
            $description
        );

        $this->uploadImageHandler->handle($command);

        return new JsonResponse(['message' => 'Image uploaded successfully'], Response::HTTP_OK);
    }

    #[Route('/profile/{profileId}', name: 'profile_image', methods: ['GET'])]
    public function getProfileImage(int $profileId): JsonResponse
    {
        $query = new getProfileImageQuery($profileId);
        $profileImage = $this->getProfileImageHandler->handle($query);

        return $this->json($profileImage);
    }

    #[Route('/{imageId}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(int $imageId): JsonResponse
    {
        $command = new DeleteImageCommand($imageId);
        $this->deleteImageHandler->handle($command);

        return new JsonResponse(['message' => 'Image deleted successfully'], Response::HTTP_OK);
    }
}