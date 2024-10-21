<?php

namespace App\Image\Application\DeleteImage;

class DeleteImageCommand
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