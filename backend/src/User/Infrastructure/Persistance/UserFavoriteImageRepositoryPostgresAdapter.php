<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistance;

use App\User\Application\Port\UserFavoriteImageRepositoryPort;
use App\User\Domain\Entity\FavoriteImage;
use Doctrine\ORM\EntityManagerInterface;

class UserFavoriteImageRepositoryPostgresAdapter implements UserFavoriteImageRepositoryPort
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

    public function findFavoriteImageIdsForUser(?int $userId, ?array $imageIds = null): array
    {
        if (!$userId) {
            return [];
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('IDENTITY(f.image) AS imageId')
            ->from(FavoriteImage::class, 'f')
            ->where('f.user = :userId')
            ->setParameter('userId', $userId);

        if (!empty($imageIds)) {
            $qb->andWhere('f.image IN (:ids)')
                ->setParameter('ids', $imageIds);
        }

        $rows = $qb->getQuery()->getArrayResult();

        return array_column($rows, 'imageId');
    }
}
