<?php

namespace App\Image\Application\UploadImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageCommand
{
    private string $imageFilename;
    private ?string $description;
    private bool $showOnHomepage;
    private string $imageType;
    private UploadedFile $imageFile;

    public function __construct(
        string $imageFilename,
        bool $showOnHomepage,
        string $imageType,
        UploadedFile $imageFile,
        ?string $description
    ) {
        $this->imageFilename = $imageFilename;
        $this->showOnHomepage = $showOnHomepage;
        $this->imageType = $imageType;
        $this->imageFile = $imageFile;
        $this->description = $description;
    }

    public function getImageFilename(): string
    {
        return $this->imageFilename;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getShowOnHomepage(): bool
    {
        return $this->showOnHomepage;
    }

    public function getImageType(): string
    {
        return $this->imageType;
    }

    public function getImageFile(): UploadedFile
    {
        return $this->imageFile;
    }
}
