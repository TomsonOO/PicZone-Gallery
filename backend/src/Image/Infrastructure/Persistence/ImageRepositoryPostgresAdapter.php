<?php

namespace App\Image\Infrastructure\Persistence;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
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

    public function findById(int $imageId): ?Image
    {
        return $this->entityManager->getRepository(Image::class)->find($imageId);
    }

    public function delete(Image $image): void
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }
}
