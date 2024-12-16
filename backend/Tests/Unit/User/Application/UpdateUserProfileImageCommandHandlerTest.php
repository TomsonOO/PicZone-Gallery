<?php

namespace App\Tests\Unit\User\Application;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommand;
use App\User\Application\UpdateUserProfileImage\UpdateUserProfileImageCommandHandler;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserProfileImageCommandHandlerTest extends TestCase
{
    private int $userId;
    private UploadedFile $profileImageFile;
    private const PROFILE_DIRECTORY = 'ProfileImages';
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private UserRepositoryPort $userRepository;
    private UpdateUserProfileImageCommandHandler $updateUserProfileImageHandler;

    protected function setUp(): void
    {
        $this->userId = 123;
        $this->profileImageFile = $this->createMock(UploadedFile::class);
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);
        $this->imageStorage = $this->createMock(ImageStoragePort::class);
        $this->userRepository = $this->createMock(UserRepositoryPort::class);

        $this->updateUserProfileImageHandler = new UpdateUserProfileImageCommandHandler(
            $this->imageRepository,
            $this->imageStorage,
            $this->userRepository
        );
    }

    public function testHandleCallsImageStorageAndRepositoriesWhenCalled(): void
    {
        $uploadedImageData = [
            'imageFilename' => 'testFilename',
            'url' => 'testUrl',
            'objectKey' => 'testObjectKey',
        ];

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->profileImageFile, self::PROFILE_DIRECTORY)
            ->willReturn($uploadedImageData);

        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $user->expects($this->once())
            ->method('setProfileImage')
            ->with($this->isInstanceOf(Image::class));

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Image::class));

        $command = new UpdateUserProfileImageCommand($this->userId, $this->profileImageFile);
        $this->updateUserProfileImageHandler->handle($command);
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

        $this->imageStorage->expects($this->never())->method('upload');

        $this->imageRepository->expects($this->never())->method('save');

        $command = new UpdateUserProfileImageCommand($this->userId, $this->profileImageFile);
        $this->updateUserProfileImageHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenImageUploadFails(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->profileImageFile, self::PROFILE_DIRECTORY)
            ->willThrowException(new \Exception('Upload failed'));

        $this->imageRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Upload failed');

        $command = new UpdateUserProfileImageCommand($this->userId, $this->profileImageFile);
        $this->updateUserProfileImageHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenSaveMethodFails(): void
    {
        $uploadedImageData = [
            'imageFilename' => 'testFilename',
            'url' => 'testUrl',
            'objectKey' => 'testObjectKey',
        ];

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->profileImageFile, self::PROFILE_DIRECTORY)
            ->willReturn($uploadedImageData);

        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($user);

        $user->expects($this->once())
            ->method('setProfileImage')
            ->with($this->isInstanceOf(Image::class));

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Image::class))
            ->willThrowException(new \Exception('Image failed to be saved in the repository.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Image failed to be saved in the repository.');

        $command = new UpdateUserProfileImageCommand($this->userId, $this->profileImageFile);
        $this->updateUserProfileImageHandler->handle($command);
    }
}
