<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class CarAd extends ClassifiedAd
{
    public static $authorizedCars = [
        'Audi' => [
            'Cabriolet', 'Q2', 'Q3', 'Q5', 'Q7', 'Q8', 'R8', 'Rs3', 'Rs4', 'Rs5', 'Rs7', 'S3', 'S4', 'S4 Avant',
            'S4 Cabriolet', 'S5', 'S7', 'S8', 'SQ5', 'SQ7', 'Tt', 'Tts', 'V8'
        ],
        'BMW' => [
            'M3', 'M4', 'M5', 'M535', 'M6', 'M635', 'Serie 1', 'Serie 2', 'Serie 3', 'Serie 4', 'Serie 5',
            'Serie 6', 'Serie 7', 'Serie 8'
        ],
        'Citroën' => [
            'C1', 'C15', 'C2', 'C25', 'C25D', 'C25E', 'C25TD', 'C3', 'C3 Aircross', 'C3 Picasso', 'C4',
            'C4 Picasso', 'C5', 'C6', 'C8', 'Ds3', 'Ds4', 'Ds5'
        ]
    ];

    #[ORM\ManyToOne(targetEntity:CarModel::class, inversedBy:'ads')]
    #[Groups(['ad:list', 'ad:show'])]
    #[Assert\NotNull]
    #[Assert\Valid]
    private $model;

    public function getModel(): ?CarModel
    {
        return $this->model;
    }

    public function setModel(CarModel $model): self
    {
        $this->model = $model;

        return $this;
    }
}