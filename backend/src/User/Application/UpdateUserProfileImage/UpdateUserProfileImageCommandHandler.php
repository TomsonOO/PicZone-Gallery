<?php

namespace App\User\Application\UpdateUserAvatar;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use App\User\Application\Port\UserRepositoryPort;

class UpdateUserAvatarCommandHandler
{
    private const PROFILE_DIRECTORY = 'ProfileImages';
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private UserRepositoryPort $userRepository;

    public function __construct(ImageRepositoryPort $imageRepository, ImageStoragePort $imageStorage, UserRepositoryPort $userRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->imageStorage = $imageStorage;
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserAvatarCommand $command): void
    {
        $uploadedImage = $this->imageStorage->upload($command->getAvatarImage(), self::PROFILE_DIRECTORY);

        $image = new Image(
            $uploadedImage['image_filename'],
            $uploadedImage['url'],
            $uploadedImage['objectKey'],
            Image::TYPE_GALLERY,
        );


    }

}