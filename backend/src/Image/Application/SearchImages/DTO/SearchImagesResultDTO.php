<?php

namespace App\Image\Application\SearchImages\DTO;

class SearchImagesResultDTO
{
    /** @var ImageDto[] */
    public array $images;
    public int $currentPage;
    public int $pageSize;
    public int $totalCount;

    public function __construct(array $images, int $currentPage, int $pageSize, int $totalCount)
    {
        $this->images = $images;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->totalCount = $totalCount;
    }
}
