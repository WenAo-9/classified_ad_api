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
            foreach ($expect as $e) {
                $this->assertTrue(in_array($e, $index));
            }
        }
    }

    public function testReIndex()
    {
        $input = [
            0 => ['406coupe'],
            1 => ['nissan350']
        ];
        $expect = [
            0 => [0 => '406', 1 => 'coupe'],
            1 => [0 => 'nissan', 1 => '350']
        ];

        foreach ($input as $key => $index) {
            $reIndex = $this->search->reIndex($index);
            $this->assertTrue(count(array_diff($reIndex, $expect[$key])) == 0);
        }
    }
}