<?php

declare(strict_types=1);

namespace App\Image\Application\SearchImages\DTO;

class SearchImagesResultDTO
{
    /** @var ImageDto[] */
    public array $images;
    public int $currentPage;
    public int $pageSize;
    public function __construct(array $images, int $currentPage, int $pageSize)
    {
        $this->images = $images;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
    }
}
