<?php

namespace App\Image\Application\ListImages;

use App\Image\Application\Port\ImageRepositoryPort;

class ListImagesQueryHandler
{
    private ImageRepositoryPort $imageRepository;

    public function __construct(ImageRepositoryPort $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function handle(ListImagesQuery $query): array
    {
        return $this->imageRepository->findAll();
    }
}