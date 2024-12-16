<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\DeleteImage\DeleteImageCommand;
use App\Image\Application\DeleteImage\DeleteImageCommandHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Domain\Exception\ImageNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DeleteImageCommandHandlerIntegrationTest extends KernelTestCase
{
    private ImageRepositoryPort $imageRepository;
    private UploadImageCommandHandler $uploadImageHandler;
    private DeleteImageCommandHandler $deleteImageHandler;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->imageRepository = self::getContainer()->get(ImageRepositoryPort::class);

        $this->uploadImageHandler = self::getContainer()->get(UploadImageCommandHandler::class);
        $this->deleteImageHandler = self::getContainer()->get(DeleteImageCommandHandler::class);
    }

    public function testDeleteDeleteImageFromRepositoryWhenCalled(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $originalFilename = 'test_image';
        $description = 'Test image description';
        $showOnHomepage = true;
        $imageType = 'gallery';

        $uploadCommand = new UploadImageCommand(
            $originalFilename,
            $showOnHomepage,
            $imageType,
            $uploadedFile,
            $description
        );

        $this->uploadImageHandler->handle($uploadCommand);

        $images = $this->imageRepository->findAll();
        $this->assertCount(1, $images);

        $image = $images[0];
        $imageId = $image->getId();

        $deleteCommand = new DeleteImageCommand($image->getId());
        $this->deleteImageHandler->handle($deleteCommand);

        $deletedImage = $this->imageRepository->findById($imageId);
        $this->assertNull($deletedImage);
    }

    public function testDeleteThrowsImageNotFoundExceptionWhenImageDoesNotExist(): void
    {
        $nonExistentImageId = 99999;
        $deleteCommand = new DeleteImageCommand($nonExistentImageId);

        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage('Image not found');

        $this->deleteImageHandler->handle($deleteCommand);
    }
}
