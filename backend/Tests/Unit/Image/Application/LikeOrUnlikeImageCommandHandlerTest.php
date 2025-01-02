<?php

namespace Tests\Unit\Image\Application;

use App\Image\Application\LikeOrUnlikeImage\LikeOrUnlikeImageCommand;
use App\Image\Application\LikeOrUnlikeImage\LikeOrUnlikeImageCommandHandler;
use App\Image\Application\Port\ImageLikeRepositoryPort;
use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Domain\Entity\Image;
use App\Image\Domain\Exception\ImageNotFoundException;
use PHPUnit\Framework\TestCase;

class LikeOrUnlikeImageCommandHandlerTest extends TestCase
{
    private int $userId;
    private int $imageId;
    private ImageLikeRepositoryPort $imageLikeRepository;
    private ImageRepositoryPort $imageRepository;
    private LikeOrUnlikeImageCommandHandler $likeOrUnlikeImageHandler;

    protected function setUp(): void
    {
        $this->userId = 213;
        $this->imageId = 123;

        $this->imageLikeRepository = $this->createMock(ImageLikeRepositoryPort::class);
        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);

        $this->likeOrUnlikeImageHandler = new LikeOrUnlikeImageCommandHandler(
            $this->imageLikeRepository,
            $this->imageRepository
        );
    }

    public function testHandleAddsLikeAndIncrementsLikeCountWhenLikeDoesNotExist(): void
    {
        $image = $this->createMock(Image::class);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->imageId)
            ->willReturn($image);

        $this->imageLikeRepository
            ->expects($this->once())
            ->method('existsByUserIdAndImageId')
            ->with($this->userId, $this->imageId)
            ->willReturn(false);

        $this->imageLikeRepository
            ->expects($this->once())
            ->method('addLike')
            ->with($this->userId, $this->imageId);

        $image
            ->expects($this->once())
            ->method('incrementLikeCount');

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($image);

        $command = new LikeOrUnlikeImageCommand($this->userId, $this->imageId);
        $this->likeOrUnlikeImageHandler->handle($command);
    }

    public function testHandleRemovesLikeAndDecrementsLikeCountWhenLikeExists(): void
    {
        $image = $this->createMock(Image::class);

        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->imageId)
            ->willReturn($image);

        $this->imageLikeRepository
            ->expects($this->once())
            ->method('existsByUserIdAndImageId')
            ->with($this->userId, $this->imageId)
            ->willReturn(true);

        $this->imageLikeRepository
            ->expects($this->once())
            ->method('removeLike')
            ->with($this->userId, $this->imageId);

        $image
            ->expects($this->once())
            ->method('decrementLikeCount');

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($image);

        $command = new LikeOrUnlikeImageCommand($this->userId, $this->imageId);
        $this->likeOrUnlikeImageHandler->handle($command);
    }

    public function testHandleThrowsImageNotFoundExceptionWhenImageDoesNotExist(): void
    {
        $this->imageRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->imageId)
            ->willReturn(null);

        $this->imageLikeRepository->expects($this->never())->method('existsByUserIdAndImageId');

        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage('Image not found');

        $command = new LikeOrUnlikeImageCommand($this->userId, $this->imageId);
        $this->likeOrUnlikeImageHandler->handle($command);
    }
}
