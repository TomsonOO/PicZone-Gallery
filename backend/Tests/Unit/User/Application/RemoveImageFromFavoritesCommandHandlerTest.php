<?php

namespace Tests\Unit\User\Application;

use App\User\Application\Port\FavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Application\RemoveImageFromFavorites\RemoveImageFromFavoritesCommand;
use App\User\Application\RemoveImageFromFavorites\RemoveImageFromFavoritesCommandHandler;
use App\User\Domain\Entity\FavoriteImage;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;

class RemoveImageFromFavoritesCommandHandlerTest extends TestCase
{
    private $userId;
    private $imageId;
    private UserRepositoryPort $userRepository;
    private FavoriteImageRepositoryPort $favoriteImageRepository;
    private RemoveImageFromFavoritesCommandHandler $removeImageFromFavoritesHandler;

    protected function setUp(): void
    {
        $this->userId = 213;
        $this->imageId = 123;
        $this->userRepository = $this->createMock(UserRepositoryPort::class);
        $this->favoriteImageRepository = $this->createMock(FavoriteImageRepositoryPort::class);

        $this->removeImageFromFavoritesHandler = new RemoveImageFromFavoritesCommandHandler($this->userRepository, $this->favoriteImageRepository);
    }

    public function testHandleCallsRepositoryMethodsWhenCalled(): void
    {
        $user = $this->createMock(User::class);
        $favoriteImage = $this->createMock(FavoriteImage::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->favoriteImageRepository
            ->expects($this->once())
            ->method('findByUserIdAndImageId')
            ->with($this->userId, $this->imageId)
            ->willReturn($favoriteImage);

        $this->favoriteImageRepository
            ->expects($this->once())
            ->method('remove')
            ->with($favoriteImage);

        $command = new RemoveImageFromFavoritesCommand($this->userId, $this->imageId);
        $this->removeImageFromFavoritesHandler->handle($command);
    }

    public function testHandleThrowsUserNotFoundExceptionWhenUserIsNotFound(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->favoriteImageRepository->expects($this->never())->method('findByUserIdAndImageId');
        $this->favoriteImageRepository->expects($this->never())->method('remove');

        $command = new RemoveImageFromFavoritesCommand($this->userId, $this->imageId);
        $this->removeImageFromFavoritesHandler->handle($command);
    }

    public function testHandleDoesNotTryToRemoveTheImageWhenImageIsNotFound(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->favoriteImageRepository
            ->expects($this->once())
            ->method('findByUserIdAndImageId')
            ->with($this->userId, $this->imageId)
            ->willReturn(null);

        $this->favoriteImageRepository->expects($this->never())->method('remove');

        $command = new RemoveImageFromFavoritesCommand($this->userId, $this->imageId);
        $this->removeImageFromFavoritesHandler->handle($command);
    }
}
