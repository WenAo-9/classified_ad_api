<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class CarBrand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    #[Groups(['car:show'])]
    private $id;

    #[ORM\Column(type:'string', length:140)]
    #[Groups(['ad:list', 'ad:show', 'car:show'])]
    private $label;

    #[ORM\OneToMany(targetEntity:CarModel::class, mappedBy:'brand')]
    private $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

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

    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(CarModel $model): self
    {
        if(!$this->models->contains($model)){
            $this->models[] = $model;
            $model->setBrand = $this;
        }

        return $this;
    }

    public function removeModel(CarModel $model): self
    {
        if ($this->models->removeElement($model)) {
            if ($model->getBrand() === $this) {
                $model->setBrand(null);
            }
        }

        return $this;
    }
}