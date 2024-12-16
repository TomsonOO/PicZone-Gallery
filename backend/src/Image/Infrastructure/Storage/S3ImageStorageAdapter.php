<?php

namespace App\Image\Infrastructure\Storage;

use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Exception\PresignedUrlGenerationException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class S3ImageStorageAdapter implements ImageStoragePort
{
    private const GALLERY_DIRECTORY = 'GalleryImages';
    private const PROFILE_DIRECTORY = 'ProfileImages';
    private S3Client $s3Client;
    private string $bucketName;
    private SluggerInterface $slugger;

    public function __construct(S3Client $s3Client, string $bucketName, SluggerInterface $slugger)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $image, string $imageType): array
    {
        $originalImageFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeImageFilename = $this->slugger->slug($originalImageFilename);
        $imageFilename = $safeImageFilename.'-'.uniqid().'.'.$image->guessExtension();

        $directory = ($imageType === 'gallery') ? self::GALLERY_DIRECTORY : self::PROFILE_DIRECTORY;
        $s3Key = $directory.'/'.$imageFilename;

        if ($image->getSize() > 2048000) {
            throw new \Exception('File size exceeds the maximum limit of 2MB.');
        }

        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $s3Key,
            'SourceFile' => $image->getRealPath(),
        ]);

        return [
            'url' => $result['ObjectURL'],
            'imageFilename' => $imageFilename,
            'objectKey' => $s3Key,
        ];
    }

    public function delete(string $objectKey): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key' => $objectKey,
        ]);
    }

    public function getPresignedUrl(string $objectKey): string
    {
        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucketName,
                'Key' => $objectKey,
            ]);

            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucketName,
                'Key' => $objectKey,
            ]);
            $request = $this->s3Client->createPresignedRequest($cmd, '+20 minutes');

            return (string) $request->getUri();
        } catch (S3Exception $e) {
            throw new PresignedUrlGenerationException('Error generating presigned URL', 0, $e);
        }
    }
}
