<?php

declare(strict_types=1);

namespace App\Image\Application\UploadImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UploadImageCommand
{
    private int $userId;
    private string $imageFilename;
    private ?string $description;
    private bool $showOnHomepage;
    private string $imageType;
    #[Assert\NotNull(message: 'Image file is required.')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        maxSizeMessage: 'The image size cannot exceed 2MB.',
        mimeTypesMessage: 'Invalid image type. Allowed types are JPEG and PNG.'
    )]
    private UploadedFile $imageFile;

    public function __construct(
        int $userId,
        string $imageFilename,
        bool $showOnHomepage,
        string $imageType,
        UploadedFile $imageFile,
        ?string $description,
    ) {
        $this->userId = $userId;
        $this->imageFilename = $imageFilename;
        $this->showOnHomepage = $showOnHomepage;
        $this->imageType = $imageType;
        $this->imageFile = $imageFile;
        $this->description = $description;
    }

    public function getUserId(): int
    {
        return $this->userId;
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
