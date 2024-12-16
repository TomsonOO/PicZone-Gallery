<?php

namespace App\Image\Application\GetPresignedUrl;

class GetPresignedUrlQuery
{
    private string $objectKey;

    public function __construct(string $objectKey)
    {
        $this->objectKey = $objectKey;
    }

    public function getObjectKey(): string
    {
        return $this->objectKey;
    }
}
