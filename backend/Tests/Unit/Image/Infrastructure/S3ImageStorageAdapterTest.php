<?php

declare(strict_types=1);

namespace Tests\Unit\Image\Infrastructure;

use App\Image\Infrastructure\Storage\S3ImageStorageAdapter;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class S3ImageStorageAdapterTest extends TestCase
{
    private string $bucketName;
    private S3ImageStorageAdapter $imageStorage;
    private S3Client $s3Client;
    private SluggerInterface $sluggerMock;

    protected function setUp(): void
    {
        $this->bucketName = 'testBucketName';
        $mock = new MockHandler();
        $mock->append(new Result([]));
        $mock->append(new Result([]));
        $this->s3Client = new S3Client([
            'region'  => 'us-west-2',
            'version' => 'latest',
            'credentials' => [
                'key'    => 'fakeAccessKeyId',
                'secret' => 'fakeSecretAccessKey',
            ],
            'handler' => $mock,
        ]);

        $this->sluggerMock = $this->createMock(SluggerInterface::class);
        $unicodeStringMock = $this->createMock(UnicodeString::class);
        $unicodeStringMock->method('toString')->willReturn('test-slug');
        $this->sluggerMock->method('slug')->willReturn($unicodeStringMock);

        $this->imageStorage = new S3ImageStorageAdapter($this->s3Client, $this->bucketName, $this->sluggerMock);
    }

    public function testUploadReturnsCorrectObjectUrlWhenCalled(): void
    {
        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->method('getClientOriginalName')->willReturn('test.jpg');
        $fileMock->method('guessExtension')->willReturn('jpg');

        $tempFile = tempnam(sys_get_temp_dir(), 'test_image');
        file_put_contents($tempFile, 'fake image content');
        $fileMock->method('getRealPath')->willReturn($tempFile);
        $fileMock->method('getSize')->willReturn(102400);

        $result = $this->imageStorage->upload($fileMock, 'gallery');

        unlink($tempFile);

        $this->assertStringStartsWith('GalleryImages/', $result['objectKey']);
    }

    public function testUploadThrowsExceptionWhenImageSizeIsExceeded(): void
    {
        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->method('getClientOriginalName')->willReturn('test.jpg');
        $fileMock->method('guessExtension')->willReturn('jpg');

        $fileMock->method('getSize')->willReturn(3068000);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File size exceeds the maximum limit of 2MB.');

        $tempFile = tempnam(sys_get_temp_dir(), 'test_image');
        file_put_contents($tempFile, 'fake image content');
        $fileMock->method('getRealPath')->willReturn($tempFile);

        $this->imageStorage->upload($fileMock, 'gallery');

        unlink($tempFile);
    }
}
