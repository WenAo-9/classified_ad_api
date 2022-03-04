<?php

namespace App\Entity;

use App\Repository\CarModelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass:CarModelRepository::class)]
class CarModel
{
    public static $commonName = [
        'cabriolet', 'coupe', 'break', 'sport'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    #[Groups(['car:show'])]
    private $id;

    #[ORM\Column(type:'string', length:140)]
    #[Groups(['ad:list', 'ad:show', 'car:show'])]
    private $label;

    #[ORM\Column(type:'boolean', nullable:true)]
    #[Groups(['car:show'])]
    private $authorized = false;

    #[ORM\ManyToOne(targetEntity:CarBrand::class, inversedBy:'models')]
    #[Groups(['ad:list', 'ad:show', 'car:show'])]
    private $brand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function isAuthorized(): ?bool
    {
        return $this->authorized;
    }

    public function setAuthorized(bool $authorized)
    {
        $this->authorized = $authorized;

        return $this;
    }

    public function getBrand(): ?CarBrand
    {
        return $this->brand;
    }

    public function setBrand(CarBrand $brand): self
    {
        $this->brand =$brand;

        return $this;
    }
}