<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\CurlHttpClient;

class CarControllerTest extends KernelTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        
        $this->client = new CurlHttpClient();
        $this->em = static::getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testGetCars()
    {
        $response = $this->client->request(
            'GET', 'http://localhost:80/car-models?page=1', [
                'verify_peer' => false,
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $rawData = $response->getContent();
        $this->assertTrue(is_string($rawData));

        $data = json_decode($rawData);
        foreach ($data as $object) {
            $this->assertObjectHasAttribute('authorized', $object);
        }
    }

    public function testGetNonExistentCar()
    {
        $response = $this->client->request(
            'GET', 'http://localhost:80/car-models?term=eoegjfg', [
                'verify_peer' => false,
            ]
        );

        $this->assertEquals(403, $response->getStatusCode());
    }
}