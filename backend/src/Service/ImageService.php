<?php

namespace App\Service;

use App\Entity\Image;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    private EntityManagerInterface $entityManager;
    private S3Client $s3Client;
    private SluggerInterface $slugger;
    private string $bucketName;

    public function __construct(EntityManagerInterface $entityManager, S3Client $s3Client, SluggerInterface $slugger, string $bucketName)
    {
        $this->entityManager = $entityManager;
        $this->s3Client = $s3Client;
        $this->slugger = $slugger;
        $this->bucketName = $bucketName;
    }

    public function uploadImage(UploadedFile $file, string $description, string $imageType, bool $showOnHomepage): Image
    {
        if ($file->getSize() > 2048000) {
            throw new \Exception("File size exceeds the maximum limit of 2MB.");
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $filename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $directory = ($imageType === 'gallery') ? 'GalleryImages' : 'ProfileImages';
        $s3Key = $directory . '/' . $filename;

        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $s3Key,
            'SourceFile' => $file->getRealPath(),
        ]);


        $image = new Image();
        $image->setFilename($filename);
        $image->setUrl($result['ObjectURL']);
        $image->setDescription($description);
        $image->setCreatedAt(new \DateTimeImmutable());
        $image->setShowOnHomepage($showOnHomepage);
        $image->setObjectKey($s3Key);
        $image->setType($imageType);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $image;
    }

    public function deleteImage(int $id): void
    {
        $image = $this->entityManager->getRepository(Image::class)->find($id);

        if (!$image) {
            throw new \InvalidArgumentException("Image not found");
        }

        $this->s3Client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key'    => $image->getObjectKey(),
        ]);

        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }


}
