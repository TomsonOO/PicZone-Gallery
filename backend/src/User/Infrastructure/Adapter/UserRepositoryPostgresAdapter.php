<?php

namespace App\User\Infrastructure\Adapter;

use App\User\Domain\Entity\User;
use App\User\Application\Port\UserRepositoryPort;
use Doctrine\ORM\EntityManagerInterface;

class UserRepositoryPostgresAdapter implements UserRepositoryPort
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function findOneByUsername(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}