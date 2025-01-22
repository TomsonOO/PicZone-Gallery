<?php

declare(strict_types=1);

namespace App\User\Application\UpdateUserProfileImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserProfileImageCommand
{
    private int $userId;
    private UploadedFile $profileImage;

    public function __construct(int $userId, UploadedFile $profileImage)
    {
        $this->userId = $userId;
        $this->profileImage = $profileImage;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProfileImage(): UploadedFile
    {
        return $this->profileImage;
    }
}
