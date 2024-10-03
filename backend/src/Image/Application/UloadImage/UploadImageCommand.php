<?php

namespace App\Image\Application\UloadImage;

class UploadImageCommand
{
    private string $filename;
    private string $url;
    private ?string $description;
    private \DateTimeImmutable $createdAt;
    private bool $showOnHomepage;
    private string $objectKey;
    private string $type;
    private $file;

    public function __construct(
        string $filename,
        string $url,
        ?string $description,
        \DateTimeImmutable $createdAt,
        bool $showOnHomepage,
        string $objectKey,
        string $type,
        $file
    ) {
        $this->filename = $filename;
        $this->url = $url;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->showOnHomepage = $showOnHomepage;
        $this->objectKey = $objectKey;
        $this->type = $type;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getShowOnHomepage(): bool
    {
        return $this->showOnHomepage;
    }

    public function getObjectKey(): string
    {
        return $this->objectKey;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFile()
    {
        return $this->file;
    }
}
