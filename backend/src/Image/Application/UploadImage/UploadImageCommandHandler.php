<?php

namespace App\Image\Application\UploadImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use App\Shared\Application\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageCommandHandler
{
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private ValidatorInterface $validator;
    public function __construct(ImageRepositoryPort $imageRepository, ImageStoragePort $imageStorage, ValidatorInterface $validator)
    {
        $this->imageRepository = $imageRepository;
        $this->imageStorage = $imageStorage;
        $this->validator = $validator;
    }

    public function handle(UploadImageCommand $command): void
    {
        $errors = $this->validator->validate($command);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $uploadedImage = $this->imageStorage->upload($command->getImageFile(), $command->getImageType());

        $image = new Image(
            $uploadedImage['imageFilename'],
            $uploadedImage['url'],
            $uploadedImage['objectKey'],
            $command->getImageType()
        );
        $image->setDescription($command->getDescription());
        $image->setShowOnHomepage($command->getShowOnHomepage());

        $this->imageRepository->save($image);
    }
}
