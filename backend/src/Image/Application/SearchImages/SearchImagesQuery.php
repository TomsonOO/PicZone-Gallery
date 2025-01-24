<?php

declare(strict_types=1);

namespace App\Image\Application\SearchImages;

class SearchImagesQuery
{
    private ?CategoryEnum $category;
    private bool $showOnHomepage;
    private ?string $searchTerm;
    private ?SortByEnum $sortBy;
    private int $pageNumber;
    private int $pageSize;
    private ?int $userId;

    public function __construct(
        ?CategoryEnum $category,
        bool $showOnHomepage,
        ?string $searchTerm,
        ?SortByEnum $sortBy,
        int $pageNumber,
        int $pageSize,
        ?int $userId,
    ) {
        $this->category = $category;
        $this->showOnHomepage = $showOnHomepage;
        $this->searchTerm = $searchTerm;
        $this->sortBy = $sortBy;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->userId = $userId;
    }

    public function getCategory(): ?CategoryEnum
    {
        return $this->category;
    }

    public function isShowOnHomepage(): bool
    {
        return $this->showOnHomepage;
    }

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    public function getSortBy(): ?SortByEnum
    {
        return $this->sortBy;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
