<?php

namespace App\User\Application\Port;

use App\User\Domain\Entity\FavoriteImage;

interface FavoriteImageRepositoryPort
{
    public function save(FavoriteImage $favoriteImage): void;

    public function remove(FavoriteImage $favoriteImage): void;

    public function findByUserIdAndImageId(int $userId, int $imageId): ?FavoriteImage;

    public function findFavoriteIdsForUser(?int $userId, array $imageIds): array;
}
