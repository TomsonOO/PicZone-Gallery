<?php

namespace App\Image\Infrastructure\Adapter;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ImageRepositoryPostgresAdapter implements ImageRepositoryPort
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Image $image): void
    {
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function findById(int $imageId): Image
    {
        $image = $this->entityManager->getRepository(Image::class)->find($imageId);

        if ($image === null) {
            throw new ImageNotFoundException("Image with ID: {$imageId} was not found.");
        }

        return $image;
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Image::class)->findAll();
    }

    public function delete(Image $image): void
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }
}