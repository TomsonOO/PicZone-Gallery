<?php

namespace App\User\Infrastructure\Persistance;

use App\User\Application\Port\FavoriteImageRepositoryPort;
use App\User\Domain\Entity\FavoriteImage;
use Doctrine\ORM\EntityManagerInterface;

class FavoriteImageRepositoryPostgresAdapter implements FavoriteImageRepositoryPort
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(FavoriteImage $favoriteImage): void
    {
        $this->entityManager->persist($favoriteImage);
        $this->entityManager->flush();
    }

    public function remove(FavoriteImage $favoriteImage): void
    {
        $this->entityManager->remove($favoriteImage);
        $this->entityManager->flush();
    }

    public function findByUserIdAndImageId(int $userId, int $imageId): ?FavoriteImage
    {
        return $this->entityManager->getRepository(FavoriteImage::class)
            ->findOneBy(['user' => $userId, 'image' => $imageId]);
    }

    public function findFavoriteIdsForUser(?int $userId, array $imageIds): array
    {
        if (!$userId || empty($imageIds)) {
            return [];
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('IDENTITY(f.image) AS imageId')
            ->from(FavoriteImage::class, 'f')
            ->where('f.user = :userId')
            ->andWhere('f.image IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $imageIds);

        $rows = $qb->getQuery()->getArrayResult();

        return array_column($rows, 'imageId');
    }
}
