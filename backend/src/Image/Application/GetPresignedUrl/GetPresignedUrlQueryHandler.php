<?php

namespace App\Image\Application\GetPresignedUrl;

use App\Image\Application\Port\ImageStoragePort;

class GetPresignedUrlQueryHandler
{
    private ImageStoragePort $imageStorage;

    public function __construct(ImageStoragePort $imageStorage)
    {
        $this->imageStorage = $imageStorage;
    }
    public function handle(GetPresignedUrlQuery $query): string
    {
        return $this->imageStorage->getPresignedUrl($query->getObjectKey());
    }

}