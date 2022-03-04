<?php

namespace App\Repository;

use App\Entity\ClassifiedAd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClassifiedAdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassifiedAd::class);
    }

    public function findWithOptions($options)
    {
        return $this->getFilteredQuery($options)
            ->orderBy('ca.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFilteredQuery($options)
    {
        $qb = $this->createQueryBuilder('ca');
        
        if (!is_null($options['discr'])) {
            $qb->andWhere('ca INSTANCE OF :adtype')
               ->setParameter('adtype', $options['discr'])
            ;
        }

        return $qb;
    }
}
