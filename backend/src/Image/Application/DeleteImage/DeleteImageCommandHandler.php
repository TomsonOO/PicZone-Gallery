<?php

namespace App\Image\Application\DeleteImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Exception\ImageNotFoundException;

class DeleteImageCommandHandler
{
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;

    public function __construct(ImageRepositoryPort $imageRepository, ImageStoragePort $imageStorage)
    {
        $this->imageRepository = $imageRepository;
        $this->imageStorage = $imageStorage;
    }

    public function handle(DeleteImageCommand $command): void
    {
        $image = $this->imageRepository->findById($command->getImageid());

        if ($image === null) {
            throw new ImageNotFoundException('Image not found');
        }

        $this->imageStorage->delete($image->getObjectKey());
        $this->imageRepository->delete($image);
    }
}