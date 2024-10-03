<?php

namespace App\User\Application\Port;

use App\User\Domain\Entity\User;

interface UserRepositoryPort
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findOneByUsername(string $username): ?User;
    public function delete(User $user): void;
}