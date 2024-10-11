<?php

namespace App\Image\Application\UploadImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;

class UploadImageCommandHandler
{
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;

    public function __construct(ImageRepositoryPort $imageRepository, ImageStoragePort $imageStorage)
    {
        $this->imageRepository = $imageRepository;
        $this->imageStorage = $imageStorage;
    }

    public function handle(UploadImageCommand $command): void
    {
        $uploadedImage = $this->imageStorage->upload($command->getImage(), $command->getImageType());

        $image = new Image(
            $uploadedImage['image_filename'],
            $uploadedImage['url'],
            $uploadedImage['objectKey'],
            $command->getImageType()
        );
        $image->setDescription($command->getDescription());
        $image->setShowOnHomepage($command->getShowOnHomepage());

        $this->imageRepository->save($image);
    }
}
