<?php

namespace App\User\Application\DeleteUser;

use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Exception\UserNotFoundException;

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
            throw new UserNotFoundException('User not found');
        }

        $this->userRepository->delete($user);
    }
}