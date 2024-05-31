<?php

namespace App\Tests\Integration\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerIntegrationTest extends WebTestCase
{
    private $client;
    private $imageRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->imageRepositoryMock = $this->createMock(ImageRepository::class);

        static::getContainer()->set(ImageRepository::class, $this->imageRepositoryMock);
    }

    public function testGetPresignedUrlSuccess()
    {
        $this->imageRepositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn([new Image()]);

        $this->client->request('GET', '/api/images/presigned-url/some-object-key');
        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($this->client->getResponse()->getContent());
    }

    public function testListImagesSuccess()
    {
        $this->imageRepositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn([new Image()]);

        $this->client->request('GET', '/api/images');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testListImagesEmpty()
    {
        $this->imageRepositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn([]);

        $this->client->request('GET', '/api/images');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    public function testUploadImageSuccess()
    {
        $pathToImage = '/var/www/tests/Resources/test_image.jpg';
        var_dump(scandir(dirname($pathToImage)));
        $this->assertFileExists($pathToImage, "Test image file does not exist.");

        $filename = 'test_image.jpg';
        $mimeType = 'image/jpeg';

        $file = new UploadedFile(
            $pathToImage,
            $filename,
            $mimeType,
            null,
            true
        );

        $this->client->request('POST', '/api/images/upload', [], ['image' => $file], [
            'CONTENT_TYPE' => 'multipart/form-data'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('Image uploaded successfully', $response['message']);

        $imageId = $response['data']['id'];

        // Clean up - remove uploaded image
        $this->client->request('DELETE', '/api/images/' . $imageId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $deleteResponse = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Image deleted successfully', $deleteResponse['message']);
    }


}
