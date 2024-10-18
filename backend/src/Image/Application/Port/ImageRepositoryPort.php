<?php

namespace App\Image\Application\Port;

use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;

interface ImageRepositoryPort
{
    public function save(Image $image): void;

    /**
     * @throws ImageNotFoundException
     */
    public function findById(int $imageId): Image;
    public function findAll(): array;
    public function delete(Image $image): void;
}