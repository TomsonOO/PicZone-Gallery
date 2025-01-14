<?php

namespace App\Image\Application\SearchImages\DTO;

class ImageDTO
{
    public int $id;
    public string $url;
    public ?string $description;
    public bool $liked;
    public bool $favorited;

    public function __construct(int $id, string $url, ?string $description, bool $liked, bool $favorited)
    {
        $this->id = $id;
        $this->url = $url;
        $this->description = $description;
        $this->liked = $liked;
        $this->favorited = $favorited;
    }
}
