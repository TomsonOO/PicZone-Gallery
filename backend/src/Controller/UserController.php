<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/api/user')]
class UserController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private ImageService $imageService;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ImageService $imageService
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->imageService = $imageService;
    }

    #[Route('', name: "add_user", methods: ["POST"])]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $userDTO = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');

        $errors = $this->validator->validate($userDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($userDTO->username);
        $user->setEmail($userDTO->email);
        $user->setPassword($passwordHasher->hashPassword($user, $userDTO->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User successfully created', 'userId' => $user->getId()],
            Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $userRepository,
    ): JsonResponse {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(['message' => 'User was not found.'], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User successfully deleted'], Response::HTTP_OK);
    }


    #[Route('/update', name: 'update_user', methods: ['PATCH'])]
    public function updateUser(Request $request): JsonResponse {
        $token = $this->security->getToken();
        $user = $token?->getUser();

        if (!$user || !is_a($user, UserInterface::class)) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $errors = $this->validator->validate($data);

        if(count($errors) > 0) {
            return $this->json(['message' => 'Validation failed', 'errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }
        if ($request->files->has('image')) {
            $file = $request->files->get('image');
            $imageType = "profile";
            if (!$file || !$file->isValid()) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, "Invalid file upload.");
            }
            try {
                $image = $this->imageService->uploadImage($file, $imageType);
            } catch (\Exception $e) {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Error processing the image.");
            }
        }

        $user->setUsername($data['username'] ?? $user->getUsername());
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setBiography($data['biography'] ?? $user->getBiography());
        $user->setIsProfilePublic($data['isProfilePublic'] ?? $user->getIsProfilePublic());
        $user->setProfileImage($image->getId() ?? $user->getProfileImage());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Profile update successfully',
            'data' => $this->serializer->serialize($user, 'json')
        ], Response::HTTP_OK);
    }

}
