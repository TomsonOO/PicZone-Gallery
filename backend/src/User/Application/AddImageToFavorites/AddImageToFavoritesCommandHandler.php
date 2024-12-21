<?php

namespace App\User\Application\AddImageToFavorites;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Exception\ImageNotFoundException;
use App\User\Application\Port\FavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\FavoriteImage;
use App\User\Domain\Exception\UserNotFoundException;

class AddImageToFavoritesCommandHandler
{
    private UserRepositoryPort $userRepository;
    private ImageRepositoryPort $imageRepository;
    private FavoriteImageRepositoryPort $favoriteImageRepository;

    public function __construct(
        UserRepositoryPort $userRepository,
        ImageRepositoryPort $imageRepository,
        FavoriteImageRepositoryPort $favoriteImageRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->favoriteImageRepository = $favoriteImageRepository;
    }

    public function handle(AddImageToFavoritesCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());
        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $image = $this->imageRepository->findById($command->getImageId());
        if ($image === null) {
            throw new ImageNotFoundException('Image not found');
        }

        $favorite = new FavoriteImage($user, $image);

        if (!$this->favoriteImageRepository->findByUserIdAndImageId($command->getUserId(), $command->getImageId())) {
            $this->favoriteImageRepository->save($favorite);
        }
    }
}
