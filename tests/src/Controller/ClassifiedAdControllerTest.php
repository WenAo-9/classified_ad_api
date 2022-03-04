<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\CurlHttpClient;
use App\Entity\CarModel;
use App\Entity\ClassifiedAd;

class ClassifiedAdControllerTest extends KernelTestCase
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

    public function testGetClassifiedAds()
    {
        $response = $this->client->request(
            'GET', 'http://localhost:80/classified-ads', [
                'verify_peer' => false,
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());

        $rawData = $response->getContent();
        $this->assertTrue(is_string($rawData));

        $data = json_decode($rawData);
        foreach ($data as $object) {
            $this->assertObjectHasAttribute('model', $object);
        }
    }

    public function testCreateClassifiedAdWrongRoute()
    {
        $response = $this->client->request(
            'POST', 'http://localhost:80/classified-ads', [
                'verify_peer' => false,
                'json' => []
            ]
        );
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testCreateClassifiedAdInvalidType()
    {
        $response = $this->client->request(
            'POST', 'http://localhost:80/classified-ads/Faux', [
                'verify_peer' => false,
                'json' => ['title' => 'test title', 'content' => 'test content', 'model' => 80]
            ]
        );
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateClassifiedAdInvalidData()
    {
        $response = $this->client->request(
            'POST', 'http://localhost:80/classified-ads/Automobile', [
                'verify_peer' => false,
                'json' => ['title' => '', 'content' => 'test content', 'model' => 8000]
            ]
        );
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateClassifiedAd()
    {
        $car = $this->em->getRepository(CarModel::class)->findOneByAuthorized(true);

        $response = $this->client->request(
            'POST', 'http://localhost:80/classified-ads/Automobile', [
                'verify_peer' => false,
                'json' => ['title' => 'title content', 'content' => 'test content', 'model' => $car->getId()]
            ]
        );
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testDeleteInvalidClassifiedAd()
    {
        $response = $this->client->request(
            'DELETE', 'http://localhost:80/classified-ads/8000', [
                'verify_peer' => false,
            ]
        );
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->client->request(
            'DELETE', 'http://localhost:80/classified-ads/xyz', [
                'verify_peer' => false,
            ]
        );
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testUpdateClassifiedAd()
    {
        $classifiedAd = $this->em->getRepository(ClassifiedAd::class)->findOneBy([]);
        
        $response = $this->client->request(
            'PUT', 'http://localhost:80/classified-ads/'.$classifiedAd->getId(), [
                'verify_peer' => false,
                'json' => ['title' => '', 'content' => 'test content']
            ]
        );
        $this->assertEquals(400, $response->getStatusCode());
    }
}