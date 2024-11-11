<?php

namespace App\User\Application\CreateUser;

use Symfony\Component\Validator\Constraints as Assert;
use App\User\Application\Validator\Constraints as AppAssert;

class CreateUserCommand
{
    #[Assert\NotBlank(message: 'Username is required.')]
    #[Assert\Length(
        min: 6,
        max: 20,
        minMessage: 'Username must be at least {{ limit }} characters long.',
        maxMessage: 'Username cannot be longer than {{ limit }} characters.',
    )]
    #[AppAssert\UniqueUsername]
    private string $username;

    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        mode: 'html5'
    )]
    private string $email;

    #[Assert\NotBlank(message: 'Password is required.')]
    #[Assert\Length(
        min: 8,
        minMessage: 'Password must be at least {{ limit }} characters long.'
    )]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'Password must contain at least one uppercase letter.'
    )]
    #[Assert\Regex(
        pattern: '/[a-z]/',
        message: 'Password must contain at least one lowercase letter.'
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'Password must contain at least one number.'
    )]
    private string $password;

    public function __construct(string $username, string $email, string $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
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
}
