<?php

namespace App\User\Application\RemoveImageFromFavorites;

class RemoveImageFromFavoritesCommand
{
    private int $userId;
    private int $imageId;

    public function __construct(int $userId, int $imageId)
    {
        $this->userId = $userId;
        $this->imageId = $imageId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }
}
