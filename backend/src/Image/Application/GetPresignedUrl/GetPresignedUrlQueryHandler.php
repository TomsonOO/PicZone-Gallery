<?php

namespace App\Image\Application\GetPresignedUrl;

use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Exception\PresignedUrlGenerationException;

class GetPresignedUrlQueryHandler
{
    private ImageStoragePort $imageStorage;

    public function __construct(ImageStoragePort $imageStorage)
    {
        $this->imageStorage = $imageStorage;
    }
    public function handle(GetPresignedUrlQuery $query): string
    {
        try {
            return $this->imageStorage->getPresignedUrl($query->getObjectKey());
        } catch (PresignedUrlGenerationException $e) {
            throw $e;
        }
    }
}