<?php

namespace App\Image\Application\Port;

use App\Image\Domain\Entity\Image;

interface ImageRepositoryPort
{
    public function save(Image $image): void;

    public function findById(int $imageId): ?Image;

    /**
     * @param int[] $imageIds
     * @return Image[]
     */
    public function findByIds(array $imageIds): array;

    public function delete(Image $image): void;
}
