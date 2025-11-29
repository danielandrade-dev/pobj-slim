<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="f_meta")
 */
class FMeta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="data_meta", referencedColumnName="data", nullable=false)
     */
    private $dataMeta;

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
    private $metaMensal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataMeta(): ?DCalendario
    {
        return $this->dataMeta;
    }

    public function setDataMeta(?DCalendario $dataMeta): self
    {
        $this->dataMeta = $dataMeta;
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

    public function getProduto(): ?DProduto
    {
        return $this->produto;
    }

    public function setProduto(?DProduto $produto): self
    {
        $this->produto = $produto;
        return $this;
    }

    public function getMetaMensal(): ?string
    {
        return $this->metaMensal;
    }

    public function setMetaMensal(string $metaMensal): self
    {
        $this->metaMensal = $metaMensal;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}

