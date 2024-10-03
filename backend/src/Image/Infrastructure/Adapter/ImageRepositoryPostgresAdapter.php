<?php

namespace App\Image\Infrastructure\Adapter;

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

    public function findById(int $id): ?Image
    {
        return $this->entityManager->getRepository(Image::class)->find($id);
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