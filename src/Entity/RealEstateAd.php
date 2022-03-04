<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class RealEstateAd extends ClassifiedAd
{
    /**
     * real estate ad properties
     * 
     * #[ORM\ManyToOne(targetEntity:PropertyAsset::class, inversedBy:'ads')]
     * #[Groups(['ad:list', 'ad:show'])]
     * #[Assert\NotNull]
     * #[Assert\Valid]
     * private $model;
     */
}