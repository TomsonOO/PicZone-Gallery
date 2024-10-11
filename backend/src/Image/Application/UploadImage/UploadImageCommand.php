<?php

namespace App\Image\Application\UploadImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageCommand
{
    private string $filename;
    private string $url;
    private ?string $description;
    private bool $showOnHomepage;
    private string $imageType;
    private UploadedFile $file;

    public function __construct(
        string $filename,
        ?string $description,
        bool $showOnHomepage,
        string $imageType,
        UploadedFile $file
    ) {
        $this->filename = $filename;
        $this->description = $description;
        $this->showOnHomepage = $showOnHomepage;
        $this->$imageType = $imageType;
        $this->file = $file;
    }

    public function getFilename(): string
    {
        return $this->filename;
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

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
