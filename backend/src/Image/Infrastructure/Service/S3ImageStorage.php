<?php

namespace App\Image\Infrastructure\Service;

use App\Image\Application\Port\ImageStoragePort;
use Aws\S3\S3Client;

class S3ImageStorage implements ImageStoragePort
{
    private S3Client $s3Client;
    private string $bucketName;

    public function __construct(S3Client $s3Client, string $bucketName)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
    }

    public function upload($file): string
    {
        $key = uniqid('image_', true);
        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key'    => $key,
            'Body'   => fopen($file->getRealPath(), 'r'),
            'ACL'    => 'public-read',
        ]);

        return $result['ObjectURL'];
    }
}
