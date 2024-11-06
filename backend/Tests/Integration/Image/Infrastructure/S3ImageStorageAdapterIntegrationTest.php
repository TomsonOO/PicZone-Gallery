<?php

namespace App\Tests\Integration\Image\Infrastructure;

use App\Image\Application\Port\ImageStoragePort;
use App\Image\Domain\Exception\PresignedUrlGenerationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3ImageStorageAdapterIntegrationTest extends KernelTestCase
{
    private ImageStoragePort $imageStorage;
    private array $uploadedObjectKeys = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $this->imageStorage = $container->get(ImageStoragePort::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->uploadedObjectKeys as $objectKey) {
            $this->imageStorage->delete($objectKey);
        }
    }

    public function testUpload_ReturnsCorrectData_WhenValidImageAndGalleryTypeProvided(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadResult = $this->imageStorage->upload($uploadedFile, 'gallery');

        $this->uploadedObjectKeys[] = $uploadResult['objectKey'];

        $this->assertArrayHasKey('url', $uploadResult);
        $this->assertArrayHasKey('imageFilename', $uploadResult);
        $this->assertArrayHasKey('objectKey', $uploadResult);
    }

    public function testUpload_ReturnsCorrectData_WhenValidImageAndProfileTypeProvided(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadResult = $this->imageStorage->upload($uploadedFile, 'profile');

        $this->uploadedObjectKeys[] = $uploadResult['objectKey'];

        $this->assertArrayHasKey('url', $uploadResult);
        $this->assertArrayHasKey('imageFilename', $uploadResult);
        $this->assertArrayHasKey('objectKey', $uploadResult);
    }

    public function testUpload_ThrowsException_WhenImageExceedsMaxSize(): void
    {
        $testImagePath = '/var/www/Tests/Resources/large_test_image.png';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'large_test_image.png',
            'image/jpeg',
            null,
            true
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File size exceeds the maximum limit of 2MB');

        $this->imageStorage->upload($uploadedFile, 'gallery');
    }

    public function testUpload_ThrowsException_WhenImageIsNotUploadedFile(): void
    {
        $this->expectException(\TypeError::class);
        $this->imageStorage->upload('not_a_file', 'gallery');
    }

    public function testDelete_RemovesObjectFromS3_WhenObjectKeyExists(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadResult = $this->imageStorage->upload($uploadedFile, 'gallery');
        $objectKey = $uploadResult['objectKey'];
        $this->uploadedObjectKeys[] = $objectKey;

        $this->imageStorage->delete($objectKey);

        $this->assertTrue(true);
    }

    public function testDelete_ThrowsException_WhenInvalidObjectKeyProvided(): void
    {
        $invalidObjectKey = '';

        $this->expectException(\InvalidArgumentException::class);
        $this->imageStorage->delete($invalidObjectKey);
    }

    public function testGetPresignedUrl_ReturnsValidUrl_WhenObjectKeyExists(): void
    {
        $testImagePath = '/var/www/Tests/Resources/test_image.jpg';

        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadResult = $this->imageStorage->upload($uploadedFile, 'gallery');

        $objectKey = $uploadResult['objectKey'];
        $this->uploadedObjectKeys[] = $objectKey;

        $presignedUrl = $this->imageStorage->getPresignedUrl($objectKey);

        $this->assertIsString($presignedUrl);
        $this->assertNotEmpty($presignedUrl);
        $this->assertStringStartsWith('http', $presignedUrl);
    }

    public function testGetPresignedUrl_ThrowsPresignedUrlGenerationException_WhenObjectKeyDoesNotExist(): void
    {
        $nonExistentObjectKey = 'nonexistentObjectKey';

        $this->expectException(PresignedUrlGenerationException::class);
        $this->expectExceptionMessage('Error generating presigned URL');

        $this->imageStorage->getPresignedUrl($nonExistentObjectKey);
    }
}
