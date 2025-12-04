<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="f_variavel")
 */
class FVariavel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DEstrutura")
     * @ORM\JoinColumn(name="funcional", referencedColumnName="funcional", nullable=false)
     */
    private $funcional;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     */
    private $meta;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     */
    private $variavel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="dt_atualizacao", referencedColumnName="data", nullable=false)
     */
    private $dtAtualizacao;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMeta(): ?string
    {
        return $this->meta;
    }

    public function setMeta(string $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getVariavel(): ?string
    {
        return $this->variavel;
    }

    public function setVariavel(string $variavel): self
    {
        $this->variavel = $variavel;
        return $this;
    }

    public function getDtAtualizacao(): ?DCalendario
    {
        return $this->dtAtualizacao;
    }

    public function setDtAtualizacao(?DCalendario $dtAtualizacao): self
    {
        $this->dtAtualizacao = $dtAtualizacao;
        return $this;
    }
}

