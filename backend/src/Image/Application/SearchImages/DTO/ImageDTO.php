<?php

declare(strict_types=1);

namespace App\Image\Application\SearchImages\DTO;

class ImageDTO
{
    public int $id;
    public string $url;
    public ?string $description;
    public int $likeCount;
    public bool $liked;
    public bool $favorited;

    public function __construct(int $id, string $url, ?string $description, int $likeCount, bool $liked, bool $favorited)
    {
        $this->id = $id;
        $this->url = $url;
        $this->description = $description;
        $this->likeCount = $likeCount;
        $this->liked = $liked;
        $this->favorited = $favorited;
    }
}
