<?php

namespace App\User\Infrastructure\Adapter;

use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\CreateUser\CreateUserCommandHandler;
use App\User\Application\DeleteUser\DeleteUserCommand;
use App\User\Application\DeleteUser\DeleteUserCommandHandler;
use App\User\Application\UpdateUser\UpdateUserCommand;
use App\User\Application\UpdateUser\UpdateUserCommandHandler;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommand;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/user')]
class WebUserAdapter extends AbstractController
{
    private Security $security;
    private CreateUserCommandHandler $createUserHandler;
    private DeleteUserCommandHandler $deleteUserHandler;
    private UpdateUserCommandHandler $updateUserHandler;
    private UpdateUserProfileImageCommandHandler $updateUserProfileImageHandler;

    public function __construct(
        Security                             $security,
        CreateUserCommandHandler             $createUserHandler,
        DeleteUserCommandHandler             $deleteUserHandler,
        UpdateUserCommandHandler             $updateUserHandler,
        UpdateUserProfileImageCommandHandler $updateUserProfileImageHandler,
    ){
        $this->security = $security;
        $this->createUserHandler = $createUserHandler;
        $this->deleteUserHandler = $deleteUserHandler;
        $this->updateUserHandler = $updateUserHandler;
        $this->updateUserProfileImageHandler = $updateUserProfileImageHandler;
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

        $this->createUserHandler->handle($command);

        return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_OK);
    }
    #[Route('/{userId}', name: "delete_user", methods: ['DELETE'])]
    public function deleteUser(int $userId): JsonResponse
    {
        $command = new DeleteUserCommand($userId);
        $this->deleteUserHandler->handle($command);

        return new JsonResponse(['message' => 'User successfully deleted'], Response::HTTP_OK);
    }

    #[Route('/update/info', name: 'update_user_info', methods: ['PATCH'])]
    public function updateUserInformation(Request $request): JsonResponse
    {
        $token = $this->security->getToken();
        $user = $token?->getUser();

        if (!$user || !is_a($user, UserInterface::class)) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $command = new UpdateUserCommand(
            $user->getId(),
            $data['email'] ?? $user->getEmail(),
            $data['username'] ?? $user->getUsername(),
            $data['biography'] ?? $user->getBiography(),
            $data['isProfilePublic'] ?? $user->getIsProfilePublic()
        );
        $this->updateUserHandler->handle($command);

        return new JsonResponse(['message' => 'User information updated successfully'], Response::HTTP_OK);
    }

    #[Route('/update/avatar', name: 'update_profile_image', methods: ['POST'])]
    public function updateUserProfileImage(Request $request): JsonResponse
    {
        $token = $this->security->getToken();
        $user = $token?->getUser();

        if (!$user || !is_a($user, UserInterface::class)) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $profileImage = $request->files->get('profile_image');
        $command = new UpdateUserProfileImageCommand($user->getId(), $profileImage);
        $this->updateUserProfileImageHandler->handle($command);

        return new JsonResponse(['message' => 'User avatar updated successfully'], Response::HTTP_OK);
    }

}