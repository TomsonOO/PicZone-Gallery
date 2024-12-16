<?php

namespace App\Image\Application\Port;

use App\Image\Domain\Entity\Image;

interface ImageRepositoryPort
{
    public function save(Image $image): void;

    public function findById(int $imageId): ?Image;

    public function findAll(): array;

    public function delete(Image $image): void;
}
