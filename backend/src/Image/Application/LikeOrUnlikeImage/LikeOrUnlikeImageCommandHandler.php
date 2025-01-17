<?php

namespace App\Image\Application\LikeOrUnlikeImage;

use App\Image\Application\Port\ImageLikeRepositoryPort;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Exception\ImageNotFoundException;

class LikeOrUnlikeImageCommandHandler
{
    private ImageLikeRepositoryPort $imageLikeRepository;
    private ImageRepositoryPort $imageRepository;

    public function __construct(ImageLikeRepositoryPort $imageLikeRepository, ImageRepositoryPort $imageRepository)
    {
        $this->imageLikeRepository = $imageLikeRepository;
        $this->imageRepository = $imageRepository;
    }

    public function handle(LikeOrUnlikeImageCommand $command): void
    {
        $userId = $command->getUserId();
        $imageId = $command->getImageId();

        $image = $this->imageRepository->findById($imageId);

        if ($image === null) {
            throw new ImageNotFoundException('Image not found');
        }

        if ($this->imageLikeRepository->existsByUserIdAndImageId($userId, $imageId)) {
            $this->imageLikeRepository->removeLike($userId, $imageId);
            $image->decrementLikeCount();
        } else {
            $this->imageLikeRepository->addLike($userId, $imageId);
            $image->incrementLikeCount();
        }

        $this->imageRepository->save($image);
    }
}
