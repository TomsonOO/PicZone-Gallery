<?php

declare(strict_types=1);

namespace App\User\Application\ToggleFavoriteImage;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Exception\ImageNotFoundException;
use App\User\Application\Port\UserFavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\FavoriteImage;
use App\User\Domain\Exception\UserNotFoundException;

class ToggleFavoriteImageCommandHandler
{
    private UserRepositoryPort $userRepository;
    private ImageRepositoryPort $imageRepository;
    private UserFavoriteImageRepositoryPort $favoriteImageRepository;

    public function __construct(
        UserRepositoryPort $userRepository,
        ImageRepositoryPort $imageRepository,
        UserFavoriteImageRepositoryPort $favoriteImageRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->favoriteImageRepository = $favoriteImageRepository;
    }

    public function handle(ToggleFavoriteImageCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());
        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $image = $this->imageRepository->findById($command->getImageId());
        if ($image === null) {
            throw new ImageNotFoundException('Image not found');
        }

        $favoriteImage = $this->favoriteImageRepository->findByUserIdAndImageId(
            $command->getUserId(),
            $command->getImageId()
        );

        if (!$favoriteImage) {
            $favoriteImage = new FavoriteImage($user, $image);
            $this->favoriteImageRepository->save($favoriteImage);
        } else {
            $this->favoriteImageRepository->remove($favoriteImage);
        }
    }
}
