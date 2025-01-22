<?php

declare(strict_types=1);

namespace App\Image\Application\GetFavoriteImages;

use App\Image\Application\Port\ImageLikeRepositoryPort;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Application\SearchImages\DTO\ImageDTO;
use App\Image\Application\SearchImages\DTO\SearchImagesResultDTO;
use App\User\Application\Port\UserFavoriteImageRepositoryPort;

class GetFavoriteImagesQueryHandler
{
    private UserFavoriteImageRepositoryPort $favoriteImageRepository;
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private ImageLikeRepositoryPort $imageLikeRepository;

    public function __construct(
        UserFavoriteImageRepositoryPort $favoriteImageRepository,
        ImageRepositoryPort $imageRepository,
        ImageStoragePort $imageStorage,
        ImageLikeRepositoryPort $imageLikeRepository
    ) {
        $this->favoriteImageRepository = $favoriteImageRepository;
        $this->imageRepository = $imageRepository;
        $this->imageStorage = $imageStorage;
        $this->imageLikeRepository = $imageLikeRepository;
    }
    public function handle(GetFavoriteImagesQuery $query): SearchImagesResultDTO
    {
        $favoriteImageIds = $this->favoriteImageRepository->findFavoriteImageIdsForUser($query->getUserId());
        $favoriteImages = $this->imageRepository->findByIds($favoriteImageIds);

        $likedImageIds = $this->imageLikeRepository->findLikedImageIdsForUser($query->getUserId(), $favoriteImageIds);

        $imageDtoList = [];
        foreach ($favoriteImages as $favoriteImage) {
            $isLiked = \in_array($favoriteImage->getId(), $likedImageIds, true);
            $presignedUrl = $this->imageStorage->getPresignedUrl($favoriteImage->getObjectKey());

            $imageDtoList[] = new ImageDTO(
                $favoriteImage->getId(),
                $presignedUrl,
                $favoriteImage->getDescription(),
                $favoriteImage->getLikeCount(),
                $isLiked,
                true
            );
        }
        return new SearchImagesResultDTO(
            $imageDtoList,
            $query->getPageNumber(),
            $query->getPageSize(),
        );
    }
}
