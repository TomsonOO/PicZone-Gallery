<?php

namespace Tests\Integration\Image\Application;

use App\Image\Application\ListImages\ListImagesQuery;
use App\Image\Application\ListImages\SearchImagesQueryHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ListImagesQueryHandlerIntegrationTest extends KernelTestCase
{
    private SearchImagesQueryHandler $handler;
    private ImageRepositoryPort $imageRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $this->imageRepository = $container->get(ImageRepositoryPort::class);
        $this->handler = new SearchImagesQueryHandler($this->imageRepository);
    }

    public function testHandleReturnsArrayOfImagesWhenImagesExist(): void
    {
        $image1 = new Image('testImage1.jpg', 'GalleryImages/testImage1.jpg', 'GalleryImages/testImage1.jpg', Image::TYPE_GALLERY);
        $image2 = new Image('testImage2.jpg', 'GalleryImages/testImage2.jpg', 'GalleryImages/testImage2.jpg', Image::TYPE_GALLERY);

        $this->imageRepository->save($image1);
        $this->imageRepository->save($image2);

        $query = new ListImagesQuery();
        $result = $this->handler->handle($query);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Image::class, $result[0]);
        $this->assertInstanceOf(Image::class, $result[1]);
    }

    public function testHandleReturnsEmptyArrayWhenNoImagesExist(): void
    {
        $query = new ListImagesQuery();
        $result = $this->handler->handle($query);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
