<?php

namespace App\Image\Application\SearchImages;

enum SortByEnum: string
{
    case LIKE_COUNT = 'likeCount';
    case CREATED_AT = 'createdAt';
}
