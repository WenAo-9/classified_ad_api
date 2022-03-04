<?php

namespace App\Repository;

use App\Entity\CarModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarModel::class);
    }

    public function findValidCarByLabel($term)
    {
        return $this->createQueryBuilder('cm')
            ->andWhere('cm.authorized = true')
            ->andWhere('LOWER(cm.label) LIKE :term')
            ->setParameter('term', $term)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findValidCars()
    {
        return $this->createQueryBuilder('cm')
            ->andWhere('cm.authorized = true')
            ->getQuery()
            ->getResult()
        ;
    }
}
