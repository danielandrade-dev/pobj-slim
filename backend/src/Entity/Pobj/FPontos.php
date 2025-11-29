<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="f_pontos")
 */
class FPontos
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DProduto")
     * @ORM\JoinColumn(name="produto_id", referencedColumnName="id", nullable=false)
     */
    private $produto;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     */
    private $meta;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     */
    private $realizado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="data_realizado", referencedColumnName="data", nullable=true)
     */
    private $dataRealizado;

    /**
     * @ORM\Column(type="datetime")
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

    public function getProduto(): ?DProduto
    {
        return $this->produto;
    }

    public function setProduto(?DProduto $produto): self
    {
        $this->produto = $produto;
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

    public function getRealizado(): ?string
    {
        return $this->realizado;
    }

    public function setRealizado(string $realizado): self
    {
        $this->realizado = $realizado;
        return $this;
    }

    public function getDataRealizado(): ?DCalendario
    {
        return $this->dataRealizado;
    }

    public function setDataRealizado(?DCalendario $dataRealizado): self
    {
        $this->dataRealizado = $dataRealizado;
        return $this;
    }

    public function getDtAtualizacao(): ?\DateTimeInterface
    {
        return $this->dtAtualizacao;
    }

    public function setDtAtualizacao(\DateTimeInterface $dtAtualizacao): self
    {
        $this->dtAtualizacao = $dtAtualizacao;
        return $this;
    }
}

