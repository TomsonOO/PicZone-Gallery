<?php

namespace App\Image\Application\Port;

use App\Image\Application\SearchImages\SearchImagesCriteria;

interface ImageSearchPort
{
    public function searchImages(SearchImagesCriteria $searchCriteria): array;
}
