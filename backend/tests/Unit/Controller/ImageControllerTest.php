<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImageControllerTest extends WebTestCase
{
    public function testListImages()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/images');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

}
