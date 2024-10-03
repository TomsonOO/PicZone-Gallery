<?php

namespace App\User\Application\RegisterUser;

class RegisterUserCommand
{
    private string $username;
    private string $email;
    private string $password;
    private ?string $biography;
    private bool $isProfilePublic;

    public function __construct(
        string $username,
        string $email,
        string $password,
        ?string $biography,
        bool $isProfilePublic
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->biography = $biography;
        $this->isProfilePublic = $isProfilePublic;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function isProfilePublic(): bool
    {
        return $this->isProfilePublic;
    }
}
