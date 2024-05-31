<?php
namespace App\Tests\Unit\Controller;

use App\Controller\UserController;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class UserControllerTest extends WebTestCase
{
    private $securityMock;
    private $entityManagerMock;
    private $serializerMock;
    private $validatorMock;
    private $passwordHasherMock;
    private $userController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityMock = $this->createMock(Security::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);

        $this->userController = new UserController(
            $this->securityMock,
            $this->entityManagerMock,
            $this->serializerMock,
            $this->validatorMock
        );

        self::bootKernel();
        $container = self::getContainer();
        $this->userController->setContainer($container);
    }

    public function testCreateUserSuccess()
    {
        $userDTO = new UserDTO();
        $userDTO->username = 'WillyWonka';
        $userDTO->email = 'WillyWonka@gmail.com';
        $userDTO->password = 'password123';

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($this->isType('string'), UserDTO::class, 'json')
            ->willReturn($userDTO);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($userDTO)
            ->willReturn(new ConstraintViolationList());

        $this->passwordHasherMock->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'password123')
            ->willReturn('hashedpassword');

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], [], [], [], [], [], json_encode([
            'username' => 'WillyWonka',
            'email' => 'WillyWonka@gmail.com',
            'password' => 'password123'
        ]));

        $response = $this->userController->createUser($request, $this->passwordHasherMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User successfully created', $responseData['message']);
        $this->assertArrayHasKey('userId', $responseData);
    }

    public function testDeleteUserSuccess()
    {
        $userId = 1;

        $user = new User();
        $user->setId($userId);
        $user->setUsername('WillyWonka');
        $user->setEmail('WillyWonka@gmail.com');

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($user);
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request();

        $response = $this->userController->deleteUser($userId, $userRepositoryMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User successfully deleted', $responseData['message']);
    }

    public function testDeleteUserNotFound()
    {
        $userId = 1;

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null);

        $this->entityManagerMock->expects($this->never())
            ->method('remove');
        $this->entityManagerMock->expects($this->never())
            ->method('flush');

        $request = new Request();

        $response = $this->userController->deleteUser($userId, $userRepositoryMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User was not found.', $responseData['message']);
    }

}
