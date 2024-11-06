<?php

namespace App\Tests\Unit\User\Application;

use App\User\Application\DeleteUser\DeleteUserCommand;
use App\User\Application\DeleteUser\DeleteUserCommandHandler;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;

class DeleteUserCommandHandlerTest extends TestCase
{
    private int $userId;
    private UserRepositoryPort $userRepository;
    private DeleteUserCommandHandler $deleteUserHandler;

    protected function setUp(): void
    {
        $this->userId = 213;
        $this->userRepository = $this->createMock(UserRepositoryPort::class);

        $this->deleteUserHandler = new DeleteUserCommandHandler($this->userRepository);
    }

    public function testHandle_CallsUserRepositoryMethods_WhenCalled(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->with($user);

        $command = new DeleteUserCommand($this->userId);
        $this->deleteUserHandler->handle($command);
    }

    public function testHandle_ThrowsUserNotFoundException_WhenUserIsNotFound(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->userRepository->expects($this->never())->method('delete');

        $command = new DeleteUserCommand($this->userId);
        $this->deleteUserHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenDeleteMethodFails(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->with($user)
            ->willThrowException(new \Exception('User not found'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $command = new DeleteUserCommand($this->userId);
        $this->deleteUserHandler->handle($command);
    }

}