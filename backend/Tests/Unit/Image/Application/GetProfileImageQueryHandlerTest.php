<?php

namespace App\Tests\Unit\Image\Application;

use App\Image\Application\GetProfileImage\GetProfileImageQuery;
use App\Image\Application\GetProfileImage\GetProfileImageQueryHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use PHPUnit\Framework\TestCase;

class GetProfileImageQueryHandlerTest extends TestCase
{
    private ImageRepositoryPort $imageRepository;
    private GetProfileImageQueryHandler $getProfileImageHandler;

    protected function setUp(): void
    {
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);

        $this->getProfileImageHandler = new GetProfileImageQueryHandler($this->imageRepository);
    }

    public function testHandle_ReturnsProfileImage_WhenCalled(): void
    {
        $profileImageId = 1;

        $expectedImage = $this->createMock(Image::class);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($profileImageId)
            ->willReturn($expectedImage);

        $query = new GetProfileImageQuery($profileImageId);
        $returnedImage = $this->getProfileImageHandler->handle($query);

        $this->assertSame($expectedImage, $returnedImage);
    }

    public function testHandle_ThrowsImageNotFoundException_WhenProfileImageIsNotFound(): void
    {
        $profileImageId = 123;

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($profileImageId)
            ->willThrowException(new ImageNotFoundException('Profile image not found.'));

        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage('Profile image not found.');

        $query = new GetProfileImageQuery($profileImageId);
        $this->getProfileImageHandler->handle($query);
    }

}