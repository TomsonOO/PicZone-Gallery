<?php

declare(strict_types=1);

namespace App\User\Application\UpdateUser;

class UpdateUserCommand
{
    private int $userId;
    private ?string $username;
    private ?string $email;
    private ?string $biography;
    private ?bool $isProfilePublic;

    public function __construct(
        int $userId,
        ?string $username,
        ?string $email,
        ?string $biography,
        ?bool $isProfilePublic,
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->biography = $biography;
        $this->isProfilePublic = $isProfilePublic;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function getIsProfilePublic(): ?bool
    {
        return $this->isProfilePublic;
    }
}
