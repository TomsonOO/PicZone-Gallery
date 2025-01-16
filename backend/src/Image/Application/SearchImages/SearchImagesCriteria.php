<?php

namespace App\Image\Application\SearchImages;

class SearchImagesCriteria
{
    public ?CategoryEnum $category;
    public bool $showOnHomepage;
    public ?string $searchTerm;
    public ?SortByEnum $sortBy;
    public int $pageNumber;
    public int $pageSize;

    public function __construct(
        ?CategoryEnum $category,
        bool $showOnHomepage,
        ?string $searchTerm,
        ?SortByEnum $sortBy,
        int $pageNumber,
        int $pageSize,
    ) {
        $this->category = $category;
        $this->showOnHomepage = $showOnHomepage;
        $this->searchTerm = $searchTerm;
        $this->sortBy = $sortBy;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }
}
