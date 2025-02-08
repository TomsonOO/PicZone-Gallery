<?php

declare(strict_types=1);

namespace App\Image\Application\GetFavoriteImages;

class GetFavoriteImagesQuery
{
    private int $userId;

    private int $pageNumber;
    private int $pageSize;

    public function __construct(int $userId, int $pageNumber, int $pageSize)
    {
        $this->userId = $userId;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
}
