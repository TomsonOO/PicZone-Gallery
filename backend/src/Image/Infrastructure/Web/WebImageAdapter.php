<?php

declare(strict_types=1);

namespace App\Image\Infrastructure\Web;

use App\Image\Application\DeleteImage\DeleteImageCommand;
use App\Image\Application\DeleteImage\DeleteImageCommandHandler;
use App\Image\Application\GetFavoriteImages\GetFavoriteImagesQuery;
use App\Image\Application\GetFavoriteImages\GetFavoriteImagesQueryHandler;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQuery;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQueryHandler;
use App\Image\Application\GetProfileImage\GetProfileImageQuery;
use App\Image\Application\GetProfileImage\GetProfileImageQueryHandler;
use App\Image\Application\LikeOrUnlikeImage\LikeOrUnlikeImageCommand;
use App\Image\Application\LikeOrUnlikeImage\LikeOrUnlikeImageCommandHandler;
use App\Image\Application\SearchImages\CategoryEnum;
use App\Image\Application\SearchImages\SearchImagesQuery;
use App\Image\Application\SearchImages\SearchImagesQueryHandler;
use App\Image\Application\SearchImages\SortByEnum;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Domain\Entity\Image;
use App\Shared\Application\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/images')]
class WebImageAdapter extends AbstractController
{
    private SearchImagesQueryHandler $searchImagesHandler;

    private UploadImageCommandHandler $uploadImageHandler;
    private GetProfileImageQueryHandler $getProfileImageHandler;
    private DeleteImageCommandHandler $deleteImageHandler;
    private GetPresignedUrlQueryHandler $getPresignedUrlHandler;
    private LikeOrUnlikeImageCommandHandler $likeOrUnlikeImageHandler;
    private GetFavoriteImagesQueryHandler $getFavoriteImagesHandler;

    public function __construct(
        SearchImagesQueryHandler $searchImagesHandler,
        UploadImageCommandHandler $uploadImageHandler,
        GetProfileImageQueryHandler $getProfileImageHandler,
        DeleteImageCommandHandler $deleteImageHandler,
        GetPresignedUrlQueryHandler $getPresignedUrlHandler,
        LikeOrUnlikeImageCommandHandler $likeOrUnlikeImageHandler,
        GetFavoriteImagesQueryHandler $getFavoriteImagesHandler,
    ) {
        $this->searchImagesHandler = $searchImagesHandler;
        $this->uploadImageHandler = $uploadImageHandler;
        $this->getProfileImageHandler = $getProfileImageHandler;
        $this->deleteImageHandler = $deleteImageHandler;
        $this->getPresignedUrlHandler = $getPresignedUrlHandler;
        $this->likeOrUnlikeImageHandler = $likeOrUnlikeImageHandler;
        $this->getFavoriteImagesHandler = $getFavoriteImagesHandler;
    }

    #[Route('/presigned-url/{objectKey}', name: 'image_presigned_url', requirements: ['objectKey' => '.+'])]
    public function getPresignedUrl(string $objectKey): Response
    {
        $query = new GetPresignedUrlQuery($objectKey);
        $presignedUrl = $this->getPresignedUrlHandler->handle($query);

        return new Response($presignedUrl, Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }

    #[Route('', name: 'list_images', methods: ['GET'])]
    public function listImages(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $userId = $user instanceof UserInterface ? $user->getId() : null;
        $categoryRequest = $request->get('category');
        $showOnHomepage = (bool) $request->get('showOnHomepage', false);
        $searchTerm = $request->get('searchTerm');
        $sortRequest = $request->get('sortBy');
        $pageNumber = (int) $request->get('pageNumber', 1);
        $pageSize = (int) $request->get('pageSize', 20);

        $categoryEnum = $categoryRequest ? CategoryEnum::tryFrom($categoryRequest) : null;
        $sortEnum = $sortRequest ? SortByEnum::tryFrom($sortRequest) : null;

        $query = new SearchImagesQuery(
            $categoryEnum,
            $showOnHomepage,
            $searchTerm,
            $sortEnum,
            $pageNumber,
            $pageSize,
            $userId
        );

        $imagesDto = $this->searchImagesHandler->handle($query);

        return new JsonResponse($imagesDto, Response::HTTP_OK);
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
        $query = new GetProfileImageQuery($profileId);
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

    #[Route('/like/{imageId}', name: 'like_image', methods: ['POST'])]
    public function likeOrUnlikeImage(int $imageId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }
        $command = new LikeOrUnlikeImageCommand($user->getId(), $imageId);
        $this->likeOrUnlikeImageHandler->handle($command);

        return new JsonResponse(['message' => 'Image like added or removed'], Response::HTTP_OK);
    }
    #[Route('/favorites', name: 'return_favorite_images', methods: ['GET'])]
    public function listFavoriteImages(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $pageNumber = (int) $request->get('pageNumber', 1);
        $pageSize = (int) $request->get('pageSize', 20);

        if (!$user instanceof UserInterface) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $query = new GetFavoriteImagesQuery($user->getId(), $pageNumber, $pageSize);
        $favoriteImagesDto = $this->getFavoriteImagesHandler->handle($query);


        return new JsonResponse($favoriteImagesDto, Response::HTTP_OK);
    }
}
