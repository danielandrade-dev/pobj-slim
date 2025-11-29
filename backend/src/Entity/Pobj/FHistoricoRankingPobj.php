<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="f_historico_ranking_pobj")
 */
class FHistoricoRankingPobj
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="data", referencedColumnName="data", nullable=false)
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DEstrutura")
     * @ORM\JoinColumn(name="funcional", referencedColumnName="funcional", nullable=false)
     */
    private $funcional;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $grupo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ranking;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $realizado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?DCalendario
    {
        return $this->data;
    }

    public function setData(?DCalendario $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getFuncional(): ?DEstrutura
    {
        return $this->funcional;
    }

    public function setFuncional(?DEstrutura $funcional): self
    {
        $this->funcional = $funcional;
        return $this;
    }

    public function getGrupo(): ?int
    {
        return $this->grupo;
    }

    public function setGrupo(?int $grupo): self
    {
        $this->grupo = $grupo;
        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): self
    {
        $this->ranking = $ranking;
        return $this;
    }

    public function getRealizado(): ?string
    {
        return $this->realizado;
    }

    public function setRealizado(?string $realizado): self
    {
        $this->realizado = $realizado;
        return $this;
    }
}

