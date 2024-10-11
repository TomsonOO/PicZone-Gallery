<?php

namespace App\Image\Application\Port;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageStoragePort
{
    public function upload(UploadedFile $image, string $imageType): array;
    public function delete(string $objectKey): void;
    public function getPresignedUrl(string $objectKey): string;
}
