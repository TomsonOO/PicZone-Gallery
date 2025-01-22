<?php

declare(strict_types=1);

namespace App\Image\Application\SearchImages;

enum CategoryEnum: string
{
    case MOST_LIKED = 'mostLiked';
    case NEWEST = 'newest';
}
