<?php

namespace App\Image\Infrastructure\Persistence;

use App\Image\Application\Port\ImageLikeRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Entity\ImageLike;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ImageLikeRepositoryPostgresAdapter implements ImageLikeRepositoryPort
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function existsByUserIdAndImageId(int $userId, int $imageId): bool
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(l.id)')
            ->from(ImageLike::class, 'l')
            ->where('l.user = :userId')
            ->andWhere('l.image = :imageId')
            ->setParameter('userId', $userId)
            ->setParameter('imageId', $imageId);

        $count = (int) $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    public function findLikedImageIdsForUser(?int $userId, array $imageIds): array
    {
        if (!$userId || empty($imageIds)) {
            return [];
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('IDENTITY(il.image) AS imageId')
            ->from(ImageLike::class, 'il')
            ->where('il.user = :userId')
            ->andWhere('il.image IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $imageIds);

        $rows = $qb->getQuery()->getArrayResult();

        return array_column($rows, 'imageId');
    }

    public function addLike(int $userId, int $imageId): void
    {
        $userRef = $this->entityManager->getReference(User::class, $userId);
        $imageRef = $this->entityManager->getReference(Image::class, $imageId);

        $like = new ImageLike($userRef, $imageRef);
        $this->entityManager->persist($like);
        $this->entityManager->flush();
    }

    public function removeLike(int $userId, int $imageId): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(ImageLike::class, 'l')
            ->where('IDENTITY(l.user) = :userId')
            ->andWhere('IDENTITY(l.image) = :imageId')
            ->setParameter('userId', $userId)
            ->setParameter('imageId', $imageId)
            ->getQuery()
            ->execute();
    }
}
