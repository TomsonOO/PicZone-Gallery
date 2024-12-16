<?php

namespace App\Tests\Unit\Image\Application;

use App\Image\Application\GetPresignedUrl\GetPresignedUrlQuery;
use App\Image\Application\GetPresignedUrl\GetPresignedUrlQueryHandler;
use App\Image\Application\Port\ImageStoragePort;
use PHPUnit\Framework\TestCase;

class GetPresignedUrlQueryHandlerTest extends TestCase
{
    private ImageStoragePort $imageStorage;
    private GetPresignedUrlQueryHandler $getPresignedUrlHandler;

    protected function setUp(): void
    {
        $this->imageStorage = $this->createMock(ImageStoragePort::class);

        $this->getPresignedUrlHandler = new GetPresignedUrlQueryHandler($this->imageStorage);
    }

    public function testHandleReturnsPresignedUrlWhenCalled(): void
    {
        $objectKey = 'testObjectKey';
        $passedUrl = 'tesUrl';

        $this->imageStorage
            ->expects($this->once())
            ->method('getPresignedUrl')
            ->with($objectKey)
            ->willReturn($passedUrl);

        $query = new GetPresignedUrlQuery($objectKey);
        $returnedUrl = $this->getPresignedUrlHandler->handle($query);

        $this->assertEquals($passedUrl, $returnedUrl);
    }

    public function testHandleThrowsExceptionWhenPresignedUrlGenerationFails(): void
    {
        $objectKey = 'testObjectKey';

        $this->imageStorage
            ->expects($this->once())
            ->method('getPresignedUrl')
            ->with($objectKey)
            ->willThrowException(new \Exception('Failed to generate a presigned url.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to generate a presigned url.');

        $query = new GetPresignedUrlQuery($objectKey);

        $this->getPresignedUrlHandler->handle($query);
    }

    public function testHandleThrowsInvalidArgumentExceptionWhenObjectKeyIsInvalid(): void
    {
        $invalidObjectKey = '';

        $this->imageStorage
            ->expects($this->once())
            ->method('getPresignedUrl')
            ->with($invalidObjectKey)
            ->wilLThrowException(new \InvalidArgumentException('Invalid object key.'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid object key.');

        $query = new GetPresignedUrlQuery($invalidObjectKey);
        $this->getPresignedUrlHandler->handle($query);
    }
}
