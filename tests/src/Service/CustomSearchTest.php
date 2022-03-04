<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\CustomSearch;


class CustomSearchTest extends KernelTestCase
{
    private $client;
    private $search;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        
        $this->search = static::getContainer()->get(CustomSearch::class);
    }

    public function testIndex()
    {
        $inputsAndExpectations = [
            'sériê   5' => ['serie5'],
            'cabriolet RS 4 avent' => ['cabriolet', 'rs4', 'avent'],
            '444 ddd' => ['444', 'ddd'],
            '444 d' => ['444d'],
        ];

        foreach ($inputsAndExpectations as $input => $expect) {
            $index = $this->search->index($input);
            foreach ($index as $i) {
                $this->assertTrue(in_array($i, $expect));
            }
        }
    }
}