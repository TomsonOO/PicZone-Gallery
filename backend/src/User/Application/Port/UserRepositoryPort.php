<?php

declare(strict_types=1);

namespace App\User\Application\Port;

use App\User\Domain\Entity\User;

interface UserRepositoryPort
{
    public function save(User $user): void;

    public function findById(int $id): ?User;

    public function findOneByUsername(string $username): ?User;

    public function existsByUsername(string $username): bool;

    public function delete(User $user): void;
}
