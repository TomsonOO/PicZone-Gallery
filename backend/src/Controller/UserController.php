<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use OpenApi\Attributes as OA;


#[Route('/api/user')]
class UserController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */
    #[OA\Post(
        path: "/api/user",
        operationId: "createUser",
        description: "Registers a new user with username, email, and password.",
        summary: "Create a new user",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            description: "User data",
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["username", "email", "password"],
                    properties: [
                        new OA\Property(property: "username", type: "string"),
                        new OA\Property(property: "email", type: "string"),
                        new OA\Property(property: "password", type: "string"),
                    ]
                )
            )
        ),
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "userId", type: "integer"),
                    ],
                    type: "object"
                ),
            ),
            new OA\Response(
                response: 400,
                description: "Invalid input data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string")),
                    ],
                    type: "object"
                ),
            ),
        ]
    ),
        Route('', name: "add_user", methods: ["POST"]),
        ApiSecurity(name: "bearerAuth")]
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


    #[OA\Delete(
        path: "/api/user/{id}",
        operationId: "deleteUser",
        description: "Deletes a single user based on the user ID.",
        summary: "Delete a user",
        security: [["bearerAuth" => []]],
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the user to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User successfully deleted",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User successfully deleted"),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "User not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User was not found."),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Authentication credentials were missing or incorrect."
                        ),
                    ]
                )
            ),
        ]
    )]
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
        $user = $this->security->getToken();
//        $authorizationHeader = $request->headers->get('Authorization');
        var_dump($user);
        if(!$user){
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

//        $data = json_decode($request->getContent(), true);
//
//        $errors = $this->validator->validate($data);
//
//        if(count($errors) > 0) {
//            return $this->json(['message' => 'Validation failed', 'errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
//        }

//        $user->setUsername($data['username'] ?? $user->getUsername());
//        $user->setEmail($data['email'] ?? $user->getEmail());
//        $user->setBiography($data['biography'] ?? $user->getBiography());

//        $this->entityManager->persist($user);
//        $this->entityManager->flush();

        return $this->json([
            'message' => 'Profile update successfully',
//            'data' => $this->serializer->serialize($user, 'json')
        ], Response::HTTP_OK);



    }






}
