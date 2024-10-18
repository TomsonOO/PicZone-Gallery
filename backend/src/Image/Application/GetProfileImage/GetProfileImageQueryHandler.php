<?php

namespace App\Image\Application\GetProfileImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;

class GetProfileImageQueryHandler
{
    private ImageRepositoryPort $imageRepository;

    public function __construct(ImageRepositoryPort $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function handle(GetProfileImageQuery $query): Image
    {
        return $this->imageRepository->findById($query->getProfileImageId());
    }
}