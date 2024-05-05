<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{

    #[Assert\NotBlank(message: "Username is required.")]
    public string $username;

    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(message: "This is not valid email.")]
    public string $email;

    #[Assert\NotBlank(message: "Password is required.")]
    #[Assert\Length(min: 6, minMessage: "Password must be at least 6 characters long.")]
    public string $password;

}