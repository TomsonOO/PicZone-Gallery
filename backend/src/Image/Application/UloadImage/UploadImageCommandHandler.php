<?php

namespace App\Image\Application\UloadImage;

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
        $imageUrl = $this->imageStorage->upload($command->getFile());

        $image = new Image(
            $command->getFilename(),
            $imageUrl,
            $command->getDescription(),
            $command->getCreatedAt(),
            $command->getShowOnHomepage(),
            $command->getObjectKey(),
            $command->getType()
        );

        $this->imageRepository->save($image);
    }
}
