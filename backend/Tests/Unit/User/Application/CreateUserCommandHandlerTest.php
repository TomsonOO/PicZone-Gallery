<?php

namespace App\Tests\Unit\User\Application;

use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\CreateUser\CreateUserCommandHandler;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use App\Shared\Application\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandHandlerTest extends TestCase
{
    private string $password;
    private string $username;
    private string $email;
    private UserRepositoryPort $userRepository;
    private ValidatorInterface $validator;
    private CreateUserCommandHandler $createUserHandler;

    protected function setUp(): void
    {
        $this->password = 'testPassword';
        $this->username = 'testUsername';
        $this->email = 'testEmail@gmail.com';

        $this->userRepository = $this->createMock(UserRepositoryPort::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->createUserHandler = new CreateUserCommandHandler($this->validator, $this->userRepository);
    }

    public function testHandle_ValidatesCommandAndCallsUserRepository_WhenCalled(): void
    {
        $this->mockValidatorToPass();
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $userWithHashedPassword) {
                return $userWithHashedPassword->getUsername() === $this->username &&
                    $userWithHashedPassword->getEmail() === $this->email &&
                    password_verify($this->password, $userWithHashedPassword->getPassword());
            }));

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    public function testHandle_ThrowsValidationException_WhenValidationFails(): void
    {
        $violations = $this->createMock(ConstraintViolationList::class);
        $violations->method('count')->willReturn(1);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(CreateUserCommand::class))
            ->willReturn($violations);

        $this->expectException(ValidationException::class);

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenRepositoryFails(): void
    {
        $this->mockValidatorToPass();

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $userWithHashedPassword) {
                return $userWithHashedPassword->getUsername() === $this->username &&
                    $userWithHashedPassword->getEmail() === $this->email &&
                    password_verify($this->password, $userWithHashedPassword->getPassword());
            }))
            ->willThrowException(new \Exception('Failed to save the user in the database.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to save the user in the database.');

        $command = new CreateUserCommand($this->username, $this->email, $this->password);
        $this->createUserHandler->handle($command);
    }

    private function mockValidatorToPass(): void
    {
        $violations = $this->createMock(ConstraintViolationList::class);
        $violations->method('count')->willReturn(0);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(CreateUserCommand::class))
            ->willReturn($violations);
    }
}
