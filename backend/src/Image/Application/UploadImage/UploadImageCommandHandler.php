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
        $originalFilename = pathinfo($command->getFile()->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $filename = $safeFilename.'-'.uniqid().'.'.$command->getFile()->guessExtension();

        $directory = ($command->getImageType() === 'gallery') ? 'GalleryImages' : 'ProfileImages';
        $s3Key = $directory . '/' . $filename;

        $imageUrl = $this->imageStorage->upload($command->getFile(), $s3Key);

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
