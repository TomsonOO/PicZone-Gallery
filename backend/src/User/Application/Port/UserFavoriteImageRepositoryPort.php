<?php

namespace App\User\Application\Port;

use App\User\Domain\Entity\FavoriteImage;

interface UserFavoriteImageRepositoryPort
{
    public function save(FavoriteImage $favoriteImage): void;

    public function remove(FavoriteImage $favoriteImage): void;

    public function findByUserIdAndImageId(int $userId, int $imageId): ?FavoriteImage;

    public function findFavoriteImageIdsForUser(?int $userId, ?array $imageIds = null): array;
}
