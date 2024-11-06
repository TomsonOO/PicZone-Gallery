<?php

namespace App\Tests\Unit\Image\Application;

use App\Image\Application\Port\ImageRepositoryPort;
use App\Image\Application\Port\ImageStoragePort;
use App\Image\Application\UploadImage\UploadImageCommand;
use App\Image\Application\UploadImage\UploadImageCommandHandler;
use App\Image\Domain\Entity\Image;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageCommandHandlerTest extends TestCase
{
    private UploadedFile $imageFile;
    private string $imageType;
    private string $description;
    private bool $showOnHomepage;
    private ImageRepositoryPort $imageRepository;
    private ImageStoragePort $imageStorage;
    private UploadImageCommandHandler $uploadImageHandler;
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->imageFile = $this->createMock(UploadedFile::class);
        $this->imageType = 'gallery';
        $this->description = 'testDescription';
        $this->showOnHomepage = false;

        $this->imageRepository = $this->createMock(ImageRepositoryPort::class);
        $this->imageStorage = $this->createMock(ImageStoragePort::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->uploadImageHandler = new UploadImageCommandHandler(
            $this->imageRepository,
            $this->imageStorage,
            $this->validator
        );
    }

    public function testHandle_CallsImageStorageAndImageRepository_WhenCalled(): void
    {
        $uploadedImageData = [
            'imageFilename' => 'testFilename',
            'url' => 'testUrl',
            'objectKey' => 'testObjectKey',
            'imageType' => $this->imageType,
            'description' => $this->description,
            'showOnHomepage' => $this->showOnHomepage,
        ];

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->imageFile, $this->imageType)
            ->willReturn($uploadedImageData);

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Image $image) use ($uploadedImageData) {
                return $this->imageMatchesExpectedData($image, $uploadedImageData);
            }));

        $command = new UploadImageCommand(
            $uploadedImageData['imageFilename'],
            $this->showOnHomepage,
            $this->imageType,
            $this->imageFile,
            $this->description
        );
        $this->uploadImageHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenStorageFails(): void
    {
        $imageFilename = 'testImageFilename';

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->imageFile, $this->imageType)
            ->willThrowException(new \Exception('Failed to upload image to the storage.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to upload image to the storage.');

        $this->imageRepository->expects($this->never())->method('save');

        $command = new UploadImageCommand(
            $imageFilename,
            $this->showOnHomepage,
            $this->imageType,
            $this->imageFile,
            $this->description
        );
        $this->uploadImageHandler->handle($command);
    }

    public function testHandle_ThrowsException_WhenRepositoryFails(): void
    {
        $uploadedImageData = [
            'imageFilename' => 'testFilename',
            'url' => 'testUrl',
            'objectKey' => 'testObjectKey',
            'imageType' => $this->imageType,
            'description' => $this->description,
            'showOnHomepage' => $this->showOnHomepage,
        ];

        $this->imageStorage
            ->expects($this->once())
            ->method('upload')
            ->with($this->imageFile, $this->imageType)
            ->willReturn($uploadedImageData);

        $this->imageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Image $image) use ($uploadedImageData) {
                return $this->imageMatchesExpectedData($image, $uploadedImageData);
            }))
            ->willThrowException(new \Exception('Failed to save image in the repository.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to save image in the repository.');

        $command = new UploadImageCommand(
            $uploadedImageData['imageFilename'],
            $this->showOnHomepage,
            $this->imageType,
            $this->imageFile,
            $this->description
        );
        $this->uploadImageHandler->handle($command);
    }

    private function imageMatchesExpectedData(Image $image, array $expectedData): bool
    {
        return $image->getFilename() === $expectedData['imageFilename']
            && $image->getUrl() === $expectedData['url']
            && $image->getObjectKey() === $expectedData['objectKey']
            && $image->getType() === $expectedData['imageType']
            && $image->getDescription() === $expectedData['description']
            && $image->getShowOnHomepage() === $expectedData['showOnHomepage'];
    }
}
