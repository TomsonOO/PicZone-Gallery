<?php

namespace App\User\Application\RemoveImageFromFavorites;

use App\User\Application\Port\UserFavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Exception\UserNotFoundException;

class RemoveImageFromFavoritesCommandHandler
{
    private UserRepositoryPort $userRepository;
    private UserFavoriteImageRepositoryPort $favoriteImageRepository;

    public function __construct(
        UserRepositoryPort              $userRepository,
        UserFavoriteImageRepositoryPort $favoriteImageRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->favoriteImageRepository = $favoriteImageRepository;
    }

    public function handle(RemoveImageFromFavoritesCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());
        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $favoriteImage = $this->favoriteImageRepository->findByUserIdAndImageId($command->getUserId(), $command->getImageId());
        if ($favoriteImage !== null) {
            $this->favoriteImageRepository->remove($favoriteImage);
        }
    }
}
