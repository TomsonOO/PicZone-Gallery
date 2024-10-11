<?php

namespace App\User\Application\UpdateUserAvatar;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserAvatarCommand
{
    private int $userId;
    private UploadedFile $avatarImage;

    public function __construct(int $userId, UploadedFile $avatarImage)
    {
        $this->userId = $userId;
        $this->avatarImage = $avatarImage;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getAvatarImage(): UploadedFile
    {
        return $this->avatarImage;
    }
}