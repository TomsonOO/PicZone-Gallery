<?php

namespace App\Image\Application\GetProfileImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;

class GetProfileImageQueryHandler
{
    private ImageRepositoryPort $imageRepository;

    public function __construct(ImageRepositoryPort $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function handle(GetProfileImageQuery $query): Image
    {
        $profileImage = $this->imageRepository->findById($query->getProfileImageId());

        if ($profileImage === null) {
            throw new ImageNotFoundException('Profile image not found');
        }

        return $profileImage;
    }
}
