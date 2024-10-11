<?php

namespace App\Image\Application\DeleteImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;

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

        $this->imageStorage->delete($image->getObjectKey());
        $this->imageRepository->delete($image);
    }
}