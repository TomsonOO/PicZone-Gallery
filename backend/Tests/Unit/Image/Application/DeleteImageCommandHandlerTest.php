<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\DeleteImage\DeleteImageCommand;
use App\Image\Application\DeleteImage\DeleteImageCommandHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteImageCommandHandlerTest extends KernelTestCase
{
    private DeleteImageCommandHandler $handler;
    private ImageRepositoryPort $imageRepositoryMock;
    private ImageStoragePort $imageStorageMock;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->imageRepositoryMock = $this->createMock(ImageRepositoryPort::class);
        $this->imageStorageMock = $this->createMock(ImageStoragePort::class);

        $this->handler = new DeleteImageCommandHandler($this->imageRepositoryMock, $this->imageStorageMock);
    }

    public function testDeleteImageSuccess(): void
    {
        $imageId = 123;
        $objectKey = 'GalleryImages/image-123456.jpg';

        $image = $this->createMock(Image::class);
        $image->expects($this->once())
            ->method('getObjectKey')
            ->willReturn($objectKey);

        $this->imageRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn($image);

        $this->imageStorageMock
            ->expects($this->once())
            ->method('delete')
            ->with($objectKey);

        $this->imageRepositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($image);

        $command = new DeleteImageCommand($imageId);
        $this->handler->handle($command);
    }
}
