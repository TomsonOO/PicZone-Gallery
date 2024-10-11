<?php

namespace App\Image\Application\UploadImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageCommand
{
    private string $imageFilename;
    private string $url;
    private ?string $description;
    private bool $showOnHomepage;
    private string $imageType;
    private UploadedFile $image;

    public function __construct(
        string $imageFilename,
        ?string $description,
        bool $showOnHomepage,
        string $imageType,
        UploadedFile $image
    ) {
        $this->imageFilename = $imageFilename;
        $this->description = $description;
        $this->showOnHomepage = $showOnHomepage;
        $this->imageType = $imageType;
        $this->image = $image;
    }

    public function getImageFilename(): string
    {
        return $this->imageFilename;
    }

    public function getUrl(): string
    {
        return $this->url;
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

    public function getImage(): UploadedFile
    {
        return $this->image;
    }
}
