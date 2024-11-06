<?php

namespace App\Image\Application\Port;

use App\Image\Domain\Exception\PresignedUrlGenerationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageStoragePort
{
    public function upload(UploadedFile $image, string $imageType): array;
    public function delete(string $objectKey): void;
    /**
     * @throws PresignedUrlGenerationException
     */
    public function getPresignedUrl(string $objectKey): string;
}
