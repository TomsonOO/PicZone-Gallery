<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

class UserDTO
{
    private int $id;
    private string $username;
    private string $email;
    private ?string $biography;
    private bool $isProfilePublic;

    public function __construct(
        int $id,
        string $username,
        string $email,
        ?string $biography,
        bool $isProfilePublic,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->biography = $biography;
        $this->isProfilePublic = $isProfilePublic;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function isProfilePublic(): bool
    {
        return $this->isProfilePublic;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'biography' => $this->getBiography(),
            'isProfilePublic' => $this->isProfilePublic(),
        ];
    }
}
