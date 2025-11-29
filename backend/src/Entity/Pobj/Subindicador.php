<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="subindicador")
 */
class Subindicador
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\Indicador")
     * @ORM\JoinColumn(name="indicador_id", referencedColumnName="id", nullable=false)
     */
    private $indicador;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $nmSubindicador;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndicador(): ?Indicador
    {
        return $this->indicador;
    }

    public function setIndicador(?Indicador $indicador): self
    {
        $this->indicador = $indicador;
        return $this;
    }

    public function getNmSubindicador(): ?string
    {
        return $this->nmSubindicador;
    }

    public function setNmSubindicador(string $nmSubindicador): self
    {
        $this->nmSubindicador = $nmSubindicador;
        return $this;
    }
}

