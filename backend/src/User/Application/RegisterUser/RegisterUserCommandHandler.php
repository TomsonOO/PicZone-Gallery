<?php

namespace App\User\Application\RegisterUser;

use App\User\Application\Port\UserRepositoryPort;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\User\Domain\Entity\User;

class RegisterUserCommandHandler
{
    private UserRepositoryPort $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepositoryPort $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function handle(RegisterUserCommand $command): void
    {
        $existingUser = $this->userRepository->findOneByUsername($command->getUsername());

        if ($existingUser !== null) {
            throw new \DomainException('User already exists');
        }

        $user = new User(
            $command->getUsername(),
            $command->getEmail(),
            $this->passwordHasher->hashPassword(new User(), $command->getPassword()),
            $command->getBiography(),
            $command->isProfilePublic()
        );

        $this->userRepository->save($user);
    }
}