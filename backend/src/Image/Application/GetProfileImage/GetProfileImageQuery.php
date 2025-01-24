<?php

declare(strict_types=1);

namespace App\Image\Application\GetProfileImage;

class GetProfileImageQuery
{
    private int $profileImageId;

    public function __construct(int $profileImageId)
    {
        $this->profileImageId = $profileImageId;
    }

    public function getProfileImageId(): int
    {
        return $this->profileImageId;
    }
}
