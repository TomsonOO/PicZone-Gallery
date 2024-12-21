<?php

namespace Tests\Unit\User\Application;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use App\User\Application\AddImageToFavorites\AddImageToFavoritesCommand;
use App\User\Application\AddImageToFavorites\AddImageToFavoritesCommandHandler;
use App\User\Application\Port\FavoriteImageRepositoryPort;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Domain\Entity\FavoriteImage;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;

class AddImageToFavoritesCommandHandlerTest extends TestCase
{
    private $userId;
    private $imageId;
    private UserRepositoryPort $userRepository;
    private ImageRepositoryPort $imageRepository;
    private FavoriteImageRepositoryPort $favoriteImageRepository;
    private AddImageToFavoritesCommandHandler $addImageToFavoritesHandler;

    protected function setUp(): void
    {
        $this->userId = 213;
        $this->imageId = 123;
        $this->userRepository = $this->createMock(UserRepositoryPort::class);
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);
        $this->favoriteImageRepository = $this->createMock(FavoriteImageRepositoryPort::class);

        $this->addImageToFavoritesHandler = new AddImageToFavoritesCommandHandler($this->userRepository, $this->imageRepository, $this->favoriteImageRepository);
    }

    public function testHandleCallsAllRepositoriesMethodsWhenCalled(): void
    {
        $user = $this->createMock(User::class);
        $image = $this->createMock(Image::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->imageId)
            ->willReturn($image);

        $this->favoriteImageRepository
            ->expects($this->once())
            ->method('findByUserIdAndImageId')
            ->with($this->userId, $this->imageId)
            ->willReturn(null);

        $this->favoriteImageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (FavoriteImage $favorite) use ($user, $image) {
                return $favorite->getUser()->getId() === $user->getId()
                    && $favorite->getImage()->getId() === $image->getId();
            }));

        $command = new AddImageToFavoritesCommand($this->userId, $this->imageId);
        $this->addImageToFavoritesHandler->handle($command);
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

        $this->imageRepository->expects($this->never())->method('findById');
        $this->favoriteImageRepository->expects($this->never())->method('findByUserIdAndImageId');
        $this->favoriteImageRepository->expects($this->never())->method('save');

        $command = new AddImageToFavoritesCommand($this->userId, $this->imageId);
        $this->addImageToFavoritesHandler->handle($command);
    }

    public function testHandleThrowsImageNotFoundExceptionWhenUserIsNotFound(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->imageId)
            ->willReturn(null);

        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage('Image not found');

        $this->favoriteImageRepository->expects($this->never())->method('findByUserIdAndImageId');
        $this->favoriteImageRepository->expects($this->never())->method('save');

        $command = new AddImageToFavoritesCommand($this->userId, $this->imageId);
        $this->addImageToFavoritesHandler->handle($command);
    }
}
