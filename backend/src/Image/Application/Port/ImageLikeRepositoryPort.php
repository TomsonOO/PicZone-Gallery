<?php

namespace App\Image\Application\Port;

interface ImageLikeRepositoryPort
{
    public function existsByUserIdAndImageId(int $userId, int $imageId): bool;

    public function addLike(int $userId, int $imageId): void;

    public function removeLike(int $userId, int $imageId): void;
}
