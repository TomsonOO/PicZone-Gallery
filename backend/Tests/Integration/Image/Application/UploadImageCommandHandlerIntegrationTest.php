<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Application\Port\ImageStoragePort;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageCommandHandlerIntegrationTest extends KernelTestCase
{
    private UploadImageCommandHandler $uploadImageHandler;
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private ValidatorInterface $validator;
    private array $uploadedObjectKeys = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $this->imageRepository = $container->get(ImageRepositoryPort::class);
        $this->imageStorage = $container->get(ImageStoragePort::class);
        $this->validator = $container->get(ValidatorInterface::class);
        $this->uploadImageHandler = new UploadImageCommandHandler($this->imageRepository, $this->imageStorage, $this->validator);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->uploadedObjectKeys as $objectKey) {
            try {
                $this->imageStorage->delete($objectKey);
            } catch (Exception $e) {
            }
        }
    }

    public function testHandle_SavesImageToRepository_WithCorrectData(): void
    {
        $uploadedFile = new UploadedFile(
            '/var/www/Tests/Resources/test_image.jpg',
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $command = new UploadImageCommand('test_image.jpg', true, 'gallery', $uploadedFile, 'Test description');
        $this->uploadImageHandler->handle($command);

        $images = $this->imageRepository->findAll();
        $this->assertCount(1, $images);
        $this->assertStringStartsWith('test-image-', $images[0]->getFilename());
        $this->assertEquals('Test description', $images[0]->getDescription());
        $this->assertEquals(true, $images[0]->getShowOnHomepage());

        $this->uploadedObjectKeys[] = $images[0]->getObjectKey();
    }

    public function testHandle_DoesNotSaveImage_WhenImageUploadFails(): void
    {
        $uploadedFile = new UploadedFile(
            '/var/www/Tests/Resources/invalid_test_file.txt',
            'invalid_test_file.txt',
            'text/plain',
            null,
            true
        );

        $command = new UploadImageCommand('invalid_test_file.txt', true, 'gallery', $uploadedFile, 'Test description');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Validation failed');

        try {
            $this->uploadImageHandler->handle($command);
        } catch (Exception $e) {
            $images = $this->imageRepository->findAll();
            $this->assertCount(0, $images);
            throw $e;
        }
    }
}
