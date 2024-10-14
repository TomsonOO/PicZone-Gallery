<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\Port\ImageStoragePort;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Domain\Entity\Image;
use App\Image\Application\Port\ImageRepositoryPort;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageCommandHandlerTest extends KernelTestCase
{
    private UploadImageCommandHandler $handler;
    private ImageStoragePort $imageStorageMock;
    private ImageRepositoryPort $imageRepositoryMock;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->imageStorageMock = $this->createMock(ImageStoragePort::class);
        $this->imageRepositoryMock = $this->createMock(ImageRepositoryPort::class);

        $this->handler = new UploadImageCommandHandler($this->imageRepositoryMock, $this->imageStorageMock);
    }

    public function testUploadImageSuccess(): void
    {
        $file = new UploadedFile(
            '/var/www/Tests/Resources/test_image.jpg',
            'image.jpg',
            'image/jpeg',
            null,
            true
        );

        $imageType = 'gallery';
        $imageFilename = 'image-123456.jpg';
        $uploadedImageData = [
            'imageFilename' => $imageFilename,
            'url' => 'https://example.com/gallery/image-123456.jpg',
            'objectKey' => 'GalleryImages/image-123456.jpg',
        ];

        $this->imageStorageMock
            ->expects($this->once())
            ->method('upload')
            ->with(
                $this->isInstanceOf(UploadedFile::class),
                'gallery'
            )
            ->willReturn($uploadedImageData);

        $this->imageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Image::class));

        $command = new UploadImageCommand(
            $imageFilename,
            'This is a test description',
            true,
            $imageType,
            $file
        );

        $this->handler->handle($command);
    }

    public function testUploadImageFailure(): void
    {
        $file = new UploadedFile(
            '/var/www/Tests/Resources/test_image.jpg',
            'image.jpg',
            'image/jpeg',
            null,
            true
        );

        $imageType = 'gallery';

        $this->imageStorageMock
            ->expects($this->once())
            ->method('upload')
            ->with(
                $this->isInstanceOf(UploadedFile::class),
                'gallery'
            )
            ->willThrowException(new \Exception("File size exceeds the maximum limit of 2MB."));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("File size exceeds the maximum limit of 2MB.");

        $command = new UploadImageCommand(
            'image-123456.jpg',
            'This is a test description',
            true,
            $imageType,
            $file
        );

        $this->handler->handle($command);
    }
}
