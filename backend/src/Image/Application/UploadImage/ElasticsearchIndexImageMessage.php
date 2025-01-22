<?php

declare(strict_types=1);

namespace App\Image\Application\UploadImage;

class ElasticsearchIndexImageMessage
{
    private int $imageId;

    public function __construct(int $imageId)
    {
        $this->imageId = $imageId;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }
}
