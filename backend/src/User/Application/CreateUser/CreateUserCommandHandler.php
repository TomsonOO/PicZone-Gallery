<?php

namespace App\User\Application\CreateUser;

use App\Shared\Application\Exception\ValidationException;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandHandler
{
    private ValidatorInterface $validator;
    private UserRepositoryPort $userRepository;

    public function __construct(
        ValidatorInterface $validator,
        UserRepositoryPort $userRepository,
    ) {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    public function handle(CreateUserCommand $command): void
    {
        $errors = $this->validator->validate($command);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $hashedPassword = password_hash($command->getPassword(), PASSWORD_DEFAULT);

        $user = User::create(
            $command->getUsername(),
            $command->getEmail(),
            $hashedPassword
        );


        $this->userRepository->save($user);
    }
}
