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

    public function findValidCars($options)
    {
        return $this->getFilteredQuery($options)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFilteredQuery($options)
    {
        $qb = $this->createQueryBuilder('cm');
        
        if (!is_null($options['page']) && is_numeric($options['page'])) {
            $qb->setFirstResult(($options['page'] - 1) * 10);
        }

        if (!is_null($options['isActive'])) {
            $qb->andWhere('cm.authorized = :true')
            ->setParameter('true', $options['isActive']);
        } else {
            $qb->andWhere('cm.authorized = true');
        }

        return $qb;
    }
}
