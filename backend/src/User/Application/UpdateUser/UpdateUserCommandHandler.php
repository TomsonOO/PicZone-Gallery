<?php

namespace App\User\Application\UpdateUser;

use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;

class UpdateUserCommandHandler
{
    private UserRepositoryPort $userRepository;

    public function __construct(UserRepositoryPort $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $user->setEmail($command->getEmail());
        $user->setUsername($command->getUsername());
        $user->setBiography($command->getBiography());
        $user->setIsProfilePublic($command->getIsProfilePublic());

        $this->userRepository->save($user);
    }
}
