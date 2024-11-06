<?php

namespace App\Tests\Integration\Image\Application;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\GetProfileImage\GetProfileImageQuery;
use App\Image\Application\GetProfileImage\GetProfileImageQueryHandler;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetProfileImageQueryHandlerIntegrationTest extends KernelTestCase
{
    private GetProfileImageQueryHandler $handler;
    private ImageRepositoryPort $imageRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $this->imageRepository = $container->get(ImageRepositoryPort::class);
        $this->handler = new GetProfileImageQueryHandler($this->imageRepository);
    }

    public function testHandle_ReturnsImage_WhenImageExists(): void
    {
        $image = new Image('testProfileImage.jpg', 'ProfileImages/testProfileImage.jpg', 'ProfileImages/testProfileImage.jpg', Image::TYPE_PROFILE);
        $this->imageRepository->save($image);

        $query = new GetProfileImageQuery($image->getId());
        $result = $this->handler->handle($query);

        $this->assertInstanceOf(Image::class, $result);
        $this->assertEquals($image->getId(), $result->getId());
    }

    public function testHandle_ThrowsImageNotFoundException_WhenImageDoesNotExist(): void
    {
        $nonExistentImageId = 999;

        $query = new GetProfileImageQuery($nonExistentImageId);

        $this->expectException(ImageNotFoundException::class);
        $this->handler->handle($query);
    }
}
