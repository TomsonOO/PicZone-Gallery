<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\GetPresignedUrl\GetPresignedUrlQuery;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQueryHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Exception\PresignedUrlGenerationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GetPresignedUrlQueryHandlerIntegrationTest extends KernelTestCase
{
    private UploadImageCommandHandler $uploadImageHandler;
    private GetPresignedUrlQueryHandler $getPresignedUrlHandler;
    private ImageStoragePort $imageStorage;
    private ImageRepositoryPort $imageRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->uploadImageHandler = $container->get(UploadImageCommandHandler::class);
        $this->getPresignedUrlHandler = $container->get(GetPresignedUrlQueryHandler::class);
        $this->imageStorage = $container->get(ImageStoragePort::class);
        $this->imageRepository = $container->get(ImageRepositoryPort::class);
    }

    public function testHandle_ReturnsPresignedUrl_WhenObjectKeyExists(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadCommand = new UploadImageCommand(
            'test_image',
            true,
            'gallery',
            $uploadedFile,
            'Test image description'
        );

        $this->uploadImageHandler->handle($uploadCommand);

        $images = $this->imageRepository->findAll();
        $this->assertCount(1, $images);

        $image = $images[0];

        $query = new GetPresignedUrlQuery($image->getObjectKey());
        $presignedUrl = $this->getPresignedUrlHandler->handle($query);

        $this->assertIsString($presignedUrl);
        $this->assertNotEmpty($presignedUrl);
        $this->assertStringStartsWith('http', $presignedUrl);

        $this->imageStorage->delete($image->getObjectKey());
    }

    public function testHandle_ThrowsException_WhenObjectKeyDoesNotExist(): void
    {
        $nonExistentObjectKey = 'invalidObjectKey';

        $query = new GetPresignedUrlQuery($nonExistentObjectKey);

        $this->expectException(PresignedUrlGenerationException::class);
        $this->expectExceptionMessage('Error generating presigned URL');

        $this->getPresignedUrlHandler->handle($query);
    }
}
