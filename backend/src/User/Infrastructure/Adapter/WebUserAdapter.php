<?php

namespace App\User\Infrastructure\Adapter;

use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\CreateUser\CreateUserCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user')]
class WebUserAdapter extends AbstractController
{
    private CreateUserCommandHandler $createUserCommandHandler;

    public function __construct(CreateUserCommandHandler $createUserCommandHandler)
    {
        $this->createUserCommandHandler = $createUserCommandHandler;
    }

    #[Route('', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateUserCommand(
            $data['username'],
            $data['email'],
            $data['password']
        );

        $this->createUserCommandHandler->handle($command);

        return new JsonResponse(['message' => 'User created successfully'], 201);
    }
}
