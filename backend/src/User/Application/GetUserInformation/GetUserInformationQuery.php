<?php

declare(strict_types=1);

namespace App\User\Application\GetUserInformation;

class GetUserInformationQuery
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
