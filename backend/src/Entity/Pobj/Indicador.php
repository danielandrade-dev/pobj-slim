<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="indicador")
 */
class Indicador
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
    private $nmIndicador;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\Familia")
     * @ORM\JoinColumn(name="familia_id", referencedColumnName="id", nullable=true)
     */
    private $familia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNmIndicador(): ?string
    {
        return $this->nmIndicador;
    }

    public function setNmIndicador(string $nmIndicador): self
    {
        $this->nmIndicador = $nmIndicador;
        return $this;
    }

    public function getFamilia(): ?Familia
    {
        return $this->familia;
    }

    public function setFamilia(?Familia $familia): self
    {
        $this->familia = $familia;
        return $this;
    }
}

