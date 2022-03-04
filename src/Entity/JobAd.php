<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class JobAd extends ClassifiedAd
{
    /**
     * job ad properties
     * #[ORM\ManyToOne(targetEntity:Profession::class, inversedBy:'ads')]
     * #[Groups(['ad:list', 'ad:show'])]
     * #[Assert\NotNull]
     * #[Assert\Valid]
     * private $model;
     */
}