<?php

namespace App\Image\Application\SearchImages;

use App\Image\Application\SearchImages\DTO\ImageDTO;
use App\Image\Application\SearchImages\DTO\SearchImagesResultDTO;
use App\Image\Application\Port\ImageLikeRepositoryPort;
use App\Image\Application\Port\ImageSearchPort;
use App\User\Application\Port\FavoriteImageRepositoryPort;

class SearchImagesQueryHandler
{
    private ImageSearchPort $imageSearch;
    private ImageLikeRepositoryPort $imageLikeRepository;
    private FavoriteImageRepositoryPort $favoriteImageRepository;

    public function __construct(
        ImageSearchPort $imageSearch,
        ImageLikeRepositoryPort $imageLikeRepository,
        FavoriteImageRepositoryPort $favoriteImageRepository
    ) {
        $this->imageSearch = $imageSearch;
        $this->imageLikeRepository = $imageLikeRepository;
        $this->favoriteImageRepository = $favoriteImageRepository;
    }

    public function handle(SearchImagesQuery $query): SearchImagesResultDto
    {
        $criteria = new SearchImagesCriteria(
            $query->getCategory(),
            $query->isShowOnHomepage(),
            $query->getSearchTerm(),
            $query->getSortBy(),
            $query->getPageNumber(),
            $query->getPageSize()
        );

        $foundImages = $this->imageSearch->searchImages($criteria);

        $imageIds = [];
        foreach ($foundImages as $foundImage) {
            $imageIds[] = $foundImage->getId();
        }

        $likedImageIds = $this->imageLikeRepository->findLikedImageIdsForUser($query->getUserId(), $imageIds);
        $favoriteImageIds = $this->favoriteImageRepository->findFavoriteIdsForUser($query->getUserId(), $imageIds);

        $imageDtoList = [];
        foreach ($foundImages as $foundImage) {
            $isLiked = \in_array($foundImage->getId(), $likedImageIds, true);
            $isFavorited = \in_array($foundImage->getId(), $favoriteImageIds, true);

            $imageDtoList[] = new ImageDTO(
                $foundImage->getId(),
                $foundImage->getUrl(),
                $foundImage->getDescription(),
                $isLiked,
                $isFavorited
            );
        }

        return new SearchImagesResultDTO(
            $imageDtoList,
            $criteria->pageNumber,
            $criteria->pageSize,
            \count($imageDtoList)
        );
    }
}
