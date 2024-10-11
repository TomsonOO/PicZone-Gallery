<?php

namespace App\Image\Infrastructure\Service;

use App\Image\Application\Port\ImageStoragePort;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class S3ImageStorage implements ImageStoragePort
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
        $s3Key = $directory . '/' . $imageFilename;

        if ($image->getSize() > 2048000) {
            throw new \Exception("File size exceeds the maximum limit of 2MB.");
        }

        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $s3Key,
            'SourceFile' => $image->getRealPath(),
        ]);

        return [
            'url' => $result['ObjectURL'],
            'image_filename' => $imageFilename,
            'objectKey' => $s3Key,
        ];
    }

    public function delete(string $objectKey): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key' => $objectKey
        ]);
    }

    public function getPresignedUrl(string $objectKey): string
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_S3_REGION'],
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);
        try {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $_ENV['AWS_S3_BUCKET_NAME'],
                'Key' => $objectKey,
            ]);
            $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

            return (string)$request->getUri();
        } catch (\Exception $e) {
            return new Response('Error generating presigned URL', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}