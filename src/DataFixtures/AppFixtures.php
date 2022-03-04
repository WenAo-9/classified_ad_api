<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\CarBrand;
use App\Entity\CarModel;
use App\Entity\CarAd;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $carData = [
            'Lotus' => [
                'Elise', 'Exige', 'Evora', 'Evija', 'Emira'
            ],
            'Alfa Romeo' => [
                'Giulia', '4C', 'Stelvio', 'Giulietta'
            ]
        ];

        foreach (CarAd::$authorizedCars as $brandName => $cars) {
            $brand = new CarBrand();
            $brand->setLabel($brandName);

            $manager->persist($brand);

            foreach ($cars as $car) {
                $model = new CarModel();
                $model->setLabel($car);
                $model->setBrand($brand);
                $model->setAuthorized(true);

                $manager->persist($model);

                if ((rand(1, 2) % 2) == 0) {
                    $carAd = new CarAd();
                    $carAd->setTitle('vends '.$brandName.' '.$car);
                    $carAd->setContent($brandName.' Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium'.$car);
                    $carAd->setModel($model);

                    $manager->persist($carAd);
                }
            }
        }

        foreach ($carData as $brandName => $cars) {
            $brand = new CarBrand();
            $brand->setLabel($brandName);

            $manager->persist($brand);

            foreach ($cars as $car) {
                $model = new CarModel();
                $model->setLabel($car);
                $model->setBrand($brand);
                $model->setAuthorized(false);

                $manager->persist($model);
            }
        }

        $manager->flush();
    }
}
