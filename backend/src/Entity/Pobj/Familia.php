<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="familia")
 */
class Familia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     */
    private $nmFamilia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNmFamilia(): ?string
    {
        return $this->nmFamilia;
    }

    public function setNmFamilia(string $nmFamilia): self
    {
        $this->nmFamilia = $nmFamilia;
        return $this;
    }
}

