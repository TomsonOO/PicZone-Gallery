<?php

declare(strict_types=1);

namespace App\Image\Application\SearchImages;

enum SortByEnum: string
{
    case LIKE_COUNT = 'likeCount';
    case CREATED_AT = 'createdAt';
}
