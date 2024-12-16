<?php

namespace App\User\Application\GetUserInformation;

use App\User\Application\DTO\UserDTO;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Exception\UserNotFoundException;

class GetUserInformationQueryHandler
{
    private UserRepositoryPort $userRepository;

    public function __construct(UserRepositoryPort $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(GetUserInformationQuery $query): UserDTO
    {
        $user = $this->userRepository->findById($query->getUserId());

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        return new UserDTO(
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getBiography(),
            $user->getIsProfilePublic(),
        );
    }
}
