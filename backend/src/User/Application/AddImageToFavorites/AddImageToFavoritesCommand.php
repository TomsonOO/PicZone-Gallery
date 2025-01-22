<?php

declare(strict_types=1);

namespace App\User\Application\AddImageToFavorites;

class AddImageToFavoritesCommand
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
