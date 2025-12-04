<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="d_produtos")
 */
class DProduto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\Familia")
     * @ORM\JoinColumn(name="familia_id", referencedColumnName="id", nullable=false)
     */
    private $familia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\Indicador")
     * @ORM\JoinColumn(name="indicador_id", referencedColumnName="id", nullable=false)
     */
    private $indicador;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\Subindicador")
     * @ORM\JoinColumn(name="subindicador_id", referencedColumnName="id", nullable=true)
     */
    private $subindicador;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $peso;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $metrica;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIndicador(): ?Indicador
    {
        return $this->indicador;
    }

    public function setIndicador(?Indicador $indicador): self
    {
        $this->indicador = $indicador;
        return $this;
    }

    public function getSubindicador(): ?Subindicador
    {
        return $this->subindicador;
    }

    public function setSubindicador(?Subindicador $subindicador): self
    {
        $this->subindicador = $subindicador;
        return $this;
    }

    public function getPeso(): ?string
    {
        return $this->peso;
    }

    public function setPeso(string $peso): self
    {
        $this->peso = $peso;
        return $this;
    }

    public function getMetrica(): ?string
    {
        return $this->metrica;
    }

    public function setMetrica(string $metrica): self
    {
        $this->metrica = $metrica;
        return $this;
    }
}

