<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Web;

use App\Image\Application\GetFavoriteImages\GetFavoriteImagesQuery;
use App\User\Application\AddImageToFavorites\AddImageToFavoritesCommand;
use App\User\Application\AddImageToFavorites\AddImageToFavoritesCommandHandler;
use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\CreateUser\CreateUserCommandHandler;
use App\User\Application\DeleteUser\DeleteUserCommand;
use App\User\Application\DeleteUser\DeleteUserCommandHandler;
use App\User\Application\GetUserInformation\GetUserInformationQuery;
use App\User\Application\GetUserInformation\GetUserInformationQueryHandler;
use App\User\Application\RemoveImageFromFavorites\RemoveImageFromFavoritesCommand;
use App\User\Application\RemoveImageFromFavorites\RemoveImageFromFavoritesCommandHandler;
use App\User\Application\ToggleFavoriteImage\ToggleFavoriteImageCommand;
use App\User\Application\ToggleFavoriteImage\ToggleFavoriteImageCommandHandler;
use App\User\Application\UpdateUser\UpdateUserCommand;
use App\User\Application\UpdateUser\UpdateUserCommandHandler;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommand;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/user')]
class WebUserAdapter extends AbstractController
{
    private CreateUserCommandHandler $createUserHandler;
    private GetUserInformationQueryHandler $getUserInformationHandler;
    private DeleteUserCommandHandler $deleteUserHandler;
    private UpdateUserCommandHandler $updateUserHandler;
    private UpdateUserProfileImageCommandHandler $updateUserProfileImageHandler;
    private ToggleFavoriteImageCommandHandler $toggleFavoriteImageHandler;

    public function __construct(
        CreateUserCommandHandler $createUserHandler,
        GetUserInformationQueryHandler $getUserInformationHandler,
        DeleteUserCommandHandler $deleteUserHandler,
        UpdateUserCommandHandler $updateUserHandler,
        UpdateUserProfileImageCommandHandler $updateUserProfileImageHandler,
        ToggleFavoriteImageCommandHandler $toggleFavoriteImageHandler
    ) {
        $this->createUserHandler = $createUserHandler;
        $this->getUserInformationHandler = $getUserInformationHandler;
        $this->deleteUserHandler = $deleteUserHandler;
        $this->updateUserHandler = $updateUserHandler;
        $this->updateUserProfileImageHandler = $updateUserProfileImageHandler;
        $this->toggleFavoriteImageHandler = $toggleFavoriteImageHandler;
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

    #[Route('/profile', name: 'get_user_profile', methods: ['GET'])]
    public function getUserProfile(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user || !is_a($user, UserInterface::class)) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $query = new GetUserInformationQuery($user->getId());
        $userDto = $this->getUserInformationHandler->handle($query);

        return $this->json($userDto->toArray());
    }

    #[Route('/profile', name: 'update_user_info', methods: ['PATCH'])]
    public function updateUserInformation(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user || !is_a($user, UserInterface::class)) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $command = new UpdateUserCommand(
            $user->getId(),
            $data['username'] ?? $user->getUsername(),
            $data['email'] ?? $user->getEmail(),
            $data['biography'] ?? $user->getBiography(),
            $data['isProfilePublic'] ?? $user->getIsProfilePublic()
        );
        $this->updateUserHandler->handle($command);

        return new JsonResponse(['message' => 'User information updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{userId}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $userId): JsonResponse
    {
        $command = new DeleteUserCommand($userId);
        $this->deleteUserHandler->handle($command);

        return new JsonResponse(['message' => 'User successfully deleted'], Response::HTTP_OK);
    }

    #[Route('/update/avatar', name: 'update_profile_image', methods: ['POST'])]
    public function updateUserProfileImage(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $profileImage = $request->files->get('profile_image');
        $command = new UpdateUserProfileImageCommand($user->getId(), $profileImage);
        $this->updateUserProfileImageHandler->handle($command);

        return new JsonResponse(['message' => 'User avatar updated successfully'], Response::HTTP_OK);
    }

    #[Route('/favorites/{imageId}', name: 'add_or_remove_favorite_image', methods: ['POST'])]
    public function toggleFavoriteImage(int $imageId): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $command = new ToggleFavoriteImageCommand(
            $user->getId(),
            $imageId
        );

        $this->toggleFavoriteImageHandler->handle($command);

        return new JsonResponse(['message' => 'Image added to favorites successfully'], Response::HTTP_OK);
    }
}
