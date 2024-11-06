<?php

namespace App\Tests\Unit\User\Application;

use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\CreateUser\CreateUserCommandHandler;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandHandlerTest extends TestCase
{
    private string $password;
    private string $username;
    private string $email;
    private string $hashedPassword;
    private UserRepositoryPort $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private CreateUserCommandHandler $createUserHandler;

    protected function setUp(): void
    {
        $this->password = 'testPassword';
        $this->username = 'testUsername';
        $this->email = 'testEmail';
        $this->hashedPassword = 'testHashedPassword';

        $this->userRepository = $this->createMock(UserRepositoryPort::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->createUserHandler = new CreateUserCommandHandler($this->userRepository, $this->passwordHasher);
    }

    public function testHandle_CallsPasswordHasherAndUserRepository_WhenCalled(): void
    {
        $this->mockPasswordHasher();
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $userWithHashedPassword) {
                return $userWithHashedPassword->getUsername() === $this->username &&
                    $userWithHashedPassword->getEmail() === $this->email &&
                    $userWithHashedPassword->getPassword() === $this->hashedPassword;
            }));

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenRepositoryFails(): void
    {
        $this->mockPasswordHasher();

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $userWithHashedPassword) {
                return $userWithHashedPassword->getUsername() === $this->username &&
                    $userWithHashedPassword->getEmail() === $this->email &&
                    $userWithHashedPassword->getPassword() === $this->hashedPassword;
            }))
            ->willThrowException(new \Exception('Failed to save the user in the database.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to save the user in the database.');

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenHasherFails(): void
    {
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->callback(function (User $userWithEmptyPassword) {
                return $userWithEmptyPassword->getUsername() === $this->username &&
                    $userWithEmptyPassword->getEmail() === $this->email;
            }))
            ->willThrowException(new \Exception('Failed to hash the password.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to hash the password.');

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    private function mockPasswordHasher(): void
    {
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->callback(function (User $userWithEmptyPassword) {
                return $userWithEmptyPassword->getUsername() === $this->username &&
                    $userWithEmptyPassword->getEmail() === $this->email;
            }))
            ->willReturn($this->hashedPassword);
    }
}