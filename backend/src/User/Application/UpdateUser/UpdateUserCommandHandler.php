<?php

namespace App\User\Application\UpdateUser;

use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;

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
            throw new \DomainException('User not found');
        }

        if ($command->getUsername() !== null) {
            $user->setUsername($command->getUsername());
        }

        if ($command->getEmail() !== null) {
            $user->setEmail($command->getEmail());
        }

        if ($command->getBiography() !== null) {
            $user->setBiography($command->getBiography());
        }

        if ($command->isProfilePublic() !== null) {
            $user->setIsProfilePublic($command->isProfilePublic());
        }

        $this->userRepository->save($user);
    }
}
