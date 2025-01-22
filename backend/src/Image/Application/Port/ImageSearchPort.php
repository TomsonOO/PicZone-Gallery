<?php

declare(strict_types=1);

namespace App\Image\Application\Port;

use App\Image\Application\SearchImages\SearchImagesCriteria;

interface ImageSearchPort
{
    public function searchImages(SearchImagesCriteria $searchCriteria): array;
}
