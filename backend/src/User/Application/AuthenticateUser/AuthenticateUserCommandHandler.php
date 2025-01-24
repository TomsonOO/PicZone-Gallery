<?php

declare(strict_types=1);

namespace App\User\Application\AuthenticateUser;

use App\User\Application\Port\JWTTokenManagerPort;
use App\User\Application\Port\UserRepositoryPort;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticateUserCommandHandler
{
    private UserRepositoryPort $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerPort $jwtManager;

    public function __construct(UserRepositoryPort $userRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerPort $jwtManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

    public function handle(AuthenticateUserCommand $command): string
    {
        $user = $this->userRepository->loadUserByIdentifier($command->getUsername());

        if (!$user) {
            throw new AuthenticationException('Username not found');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $command->getPassword())) {
            throw new AuthenticationException('Incorrect password');
        }

        return $this->jwtManager->createToken($user);
    }
}
