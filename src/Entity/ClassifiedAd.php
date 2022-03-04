<?php

namespace App\Entity;

use App\Repository\ClassifiedAdRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:ClassifiedAdRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name:'adType', type:'string')]
#[ORM\DiscriminatorMap([
    'Automobile'=>'CarAd', 
    'Emploi'=>'JobAd', 
    'Immobilier'=>'RealEstateAd',
])]
class ClassifiedAd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    #[Groups(['ad:list', 'ad:show'])]
    private $id;

    #[ORM\Column(type:'string', length:140)]
    #[Groups(['ad:list', 'ad:show'])]
    #[Assert\NotBlank]
    private $title;

    #[ORM\Column(type:'text')]
    #[Groups(['ad:show'])]
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}