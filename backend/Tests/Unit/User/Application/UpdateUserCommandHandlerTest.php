<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application;

use App\User\Application\Port\UserRepositoryPort;
use App\User\Application\UpdateUser\UpdateUserCommand;
use App\User\Application\UpdateUser\UpdateUserCommandHandler;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;

class UpdateUserCommandHandlerTest extends TestCase
{
    private int $userId;
    private string $email;
    private string $username;
    private string $biography;
    private bool $isProfilePublic;
    private UserRepositoryPort $userRepository;
    private UpdateUserCommandHandler $updateUserHandler;

    protected function setUp(): void
    {
        $this->userId = 213;
        $this->email = 'testEmail@email.com';
        $this->username = 'testUsername';
        $this->biography = 'testBiography';
        $this->isProfilePublic = false;

        $this->userRepository = $this->createMock(UserRepositoryPort::class);

        $this->updateUserHandler = new UpdateUserCommandHandler($this->userRepository);
    }

    public function testHandleCallsUserRepositoryWithCorrectUserDataWhenCalled(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $user->expects($this->once())->method('setEmail')->with($this->email);
        $user->expects($this->once())->method('setUsername')->with($this->username);
        $user->expects($this->once())->method('setBiography')->with($this->biography);
        $user->expects($this->once())->method('setIsProfilePublic')->with($this->isProfilePublic);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $command = new UpdateUserCommand(
            $this->userId,
            $this->username,
            $this->email,
            $this->biography,
            $this->isProfilePublic
        );
        $this->updateUserHandler->handle($command);
    }

    public function testHandleThrowsUsetNotFoundExceptionWhenUserIsNotFound(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->userRepository->expects($this->never())->method('save');

        $command = new UpdateUserCommand(
            $this->userId,
            $this->username,
            $this->email,
            $this->biography,
            $this->isProfilePublic
        );
        $this->updateUserHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenSaveMethodFails(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user)
            ->willThrowException(new \Exception('User not found'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $command = new UpdateUserCommand(
            $this->userId,
            $this->username,
            $this->email,
            $this->biography,
            $this->isProfilePublic
        );
        $this->updateUserHandler->handle($command);
    }
}
