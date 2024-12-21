<?php

namespace Tests\Unit\Image\Application;

use App\Image\Application\ListImages\ListImagesQuery;
use App\Image\Application\ListImages\ListImagesQueryHandler;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use PHPUnit\Framework\TestCase;

class ListImagesQueryHandlerTest extends TestCase
{
    private ImageRepositoryPort $imageRepository;
    private ListImagesQueryHandler $listImagesHandler;

    protected function setUp(): void
    {
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);

        $this->listImagesHandler = new ListImagesQueryHandler($this->imageRepository);
    }

    public function testHandleReturnsArrayOfImagesWhenCalled(): void
    {
        $testImage1 = $this->createMock(Image::class);
        $testImage2 = $this->createMock(Image::class);
        $expectedImages = [$testImage1, $testImage2];

        $this->imageRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedImages);

        $query = new ListImagesQuery();
        $returnedImages = $this->listImagesHandler->handle($query);

        $this->assertSame($expectedImages, $returnedImages);
    }

    public function testHandleReturnsEmptyArrayWhenNoImagesExist(): void
    {
        $expectedImages = [];

        $this->imageRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedImages);

        $query = new ListImagesQuery();
        $returnedImages = $this->listImagesHandler->handle($query);

        $this->assertSame($expectedImages, $returnedImages);
    }
}
