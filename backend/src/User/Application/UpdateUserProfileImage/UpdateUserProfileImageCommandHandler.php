<?php

namespace App\User\Application\UpdateUserProfileImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Exception\UserNotFoundException;

class UpdateUserProfileImageCommandHandler
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

    public function handle(UpdateUserProfileImageCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());
        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }
        $uploadedImage = $this->imageStorage->upload($command->getProfileImage(), self::PROFILE_DIRECTORY);

        $profileImage = new Image(
            $uploadedImage['imageFilename'],
            $uploadedImage['url'],
            $uploadedImage['objectKey'],
            Image::TYPE_PROFILE,
        );

        $user->setProfileImage($profileImage);
        $this->imageRepository->save($profileImage);
        $this->userRepository->save($user);
    }
}