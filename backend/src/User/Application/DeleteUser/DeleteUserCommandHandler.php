<?php

namespace App\User\Application\DeleteUser;

use App\User\Application\Port\UserRepositoryPort;

class DeleteUserCommandHandler
{
    private UserRepositoryPort $userRepository;

    public function __construct(UserRepositoryPort $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());

        if ($user === null) {
            throw new \DomainException('User not found');
        }

        $this->userRepository->delete($user);
    }
}