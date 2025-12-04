<?php

namespace App\Entity\Omega;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omega_categorias")
 */
class OmegaCategoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Omega\OmegaDepartamento")
     * @ORM\JoinColumn(name="departamento_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $departamento;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $nome;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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

    public function getDepartamento(): ?OmegaDepartamento
    {
        return $this->departamento;
    }

    public function setDepartamento(?OmegaDepartamento $departamento): self
    {
        $this->departamento = $departamento;
        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getOrdem(): ?int
    {
        return $this->ordem;
    }

    public function setOrdem(?int $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
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

