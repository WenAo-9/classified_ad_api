<?php

namespace App\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validation;
use App\Entity\CarAd;
use App\Entity\CarModel;

class CarAdTest extends KernelTestCase
{
    public function testInvalidSetTitle()
    {  
        $carAd = new CarAd();
        $carAd->setTitle('');
        $carAd->setModel(new CarModel());

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
        ;

        $errors = $validator->validate($carAd);

        $this->assertCount(1, $errors);
    }

    public function testWithoutSettingModel()
    {
        $carAd = new CarAd();
        $carAd->setTitle('test');

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
        ;

        $errors = $validator->validate($carAd);

        $this->assertCount(1, $errors);
    }
}