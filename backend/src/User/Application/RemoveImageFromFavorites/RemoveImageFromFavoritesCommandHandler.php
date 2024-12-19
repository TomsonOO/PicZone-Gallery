<?php

namespace App\User\Application\RemoveImageFromFavorites;

use App\User\Application\Port\FavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Exception\UserNotFoundException;

class RemoveImageFromFavoritesCommandHandler
{
    private UserRepositoryPort $userRepository;
    private FavoriteImageRepositoryPort $favoriteImageRepository;

    public function __construct(
        UserRepositoryPort $userRepository,
        FavoriteImageRepositoryPort $favoriteImageRepository
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

        $favorite = $this->favoriteImageRepository->findByUserIdAndImageId($command->getUserId(), $command->getImageId());
        if ($favorite !== null) {
            $this->favoriteImageRepository->remove($favorite);
        }
    }
}
