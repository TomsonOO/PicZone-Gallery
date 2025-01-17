<?php

namespace Tests\Unit\Image\Application;

use App\Image\Application\DeleteImage\DeleteImageCommand;
use App\Image\Application\DeleteImage\DeleteImageCommandHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use PHPUnit\Framework\TestCase;

class DeleteImageCommandHandlerTest extends TestCase
{
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private DeleteImageCommandHandler $deleteImageHandler;

    protected function setUp(): void
    {
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);
        $this->imageStorage = $this->createMock(ImageStoragePort::class);

        $this->deleteImageHandler = new DeleteImageCommandHandler(
            $this->imageRepository,
            $this->imageStorage
        );
    }

    public function testHandleCallsImageRepositoryAndImageStorageWhenCalled(): void
    {
        $profileId = 123;
        $objectKey = 'testObjectKey';

        $image = $this->createMock(Image::class);
        $image->method('getObjectKey')->willReturn($objectKey);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($profileId)
            ->willReturn($image);

        $this->imageStorage
            ->expects($this->once())
            ->method('delete')
            ->with($objectKey);

        $this->imageRepository
            ->expects($this->once())
            ->method('delete')
            ->with($image);

        $command = new DeleteImageCommand($profileId);
        $this->deleteImageHandler->handle($command);
    }

    public function testHandleThrowsImageNotFoundExceptionWhenImageIsNotFound(): void
    {
        $imageId = 123;

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn(null);

        $this->imageStorage->expects($this->never())->method('delete');
        $this->imageRepository->expects($this->never())->method('delete');

        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage('Image not found');

        $command = new DeleteImageCommand($imageId);
        $this->deleteImageHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenStorageFails(): void
    {
        $imageId = 123;
        $image = $this->createMock(Image::class);
        $objectKey = 'testObjectKey';

        $image->method('getObjectKey')->willReturn($objectKey);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn($image);

        $this->imageStorage
            ->expects($this->once())
            ->method('delete')
            ->with($objectKey)
            ->willThrowException(new \Exception('Failed to delete image from the storage.'));

        $this->imageRepository->expects($this->never())->method('delete');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to delete image from the storage.');

        $command = new DeleteImageCommand($imageId);
        $this->deleteImageHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenRepositoryFails(): void
    {
        $imageId = 123;
        $image = $this->createMock(Image::class);
        $objectKey = 'testObjectKey';

        $image->method('getObjectKey')->willReturn($objectKey);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn($image);

        $this->imageStorage
            ->expects($this->once())
            ->method('delete')
            ->with($objectKey);

        $this->imageRepository
            ->expects($this->once())
            ->method('delete')
            ->with($image)
            ->willThrowException(new \Exception('Failed to delete the image from the database.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to delete the image from the database.');

        $command = new DeleteImageCommand($imageId);
        $this->deleteImageHandler->handle($command);
    }
}
