<?php

namespace App\Tests\Unit\Image\Infrastructure;

use App\Image\Domain\Entity\Image;
use App\Image\Infrastructure\Persistence\ImageRepositoryPostgresAdapter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ImageRepositoryPostgresAdapterTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ImageRepositoryPostgresAdapter $imageRepositoryAdapter;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->imageRepositoryAdapter = new ImageRepositoryPostgresAdapter($this->entityManager);
    }

    public function testSaveCallsPersistsAndFlushWhenCalled(): void
    {
        $image = $this->createMock(Image::class);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($image);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->imageRepositoryAdapter->save($image);
    }
}
