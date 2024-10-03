<?php

namespace App\User\Application\CreateUser;

use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandHandler
{
    private UserRepositoryPort $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepositoryPort $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function handle(CreateUserCommand $command): void
    {
        $user = new User(
            $command->getUsername(),
            $command->getEmail(),
            $command->getEmail(),
//            $this->passwordHasher->hashPassword(new User(), $command->getPassword())
        );

        $this->userRepository->save($user);
    }
}
