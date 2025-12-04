<?php

namespace App\Entity\Omega;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omega_status")
 */
class OmegaStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=40)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $tone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @ORM\Column(name="departamento_id", type="string", length=30, nullable=true)
     */
    private $departamentoId;

    
    private $departamento;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getTone(): ?string
    {
        return $this->tone;
    }

    public function setTone(string $tone): self
    {
        $this->tone = $tone;
        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
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

    public function getDepartamentoId(): ?string
    {
        return $this->departamentoId;
    }

    public function setDepartamentoId(?string $departamentoId): self
    {
        $this->departamentoId = $departamentoId;
        return $this;
    }

    public function getDepartamento(): ?OmegaDepartamento
    {
        return $this->departamento;
    }

    public function setDepartamento(?OmegaDepartamento $departamento): self
    {
        $this->departamento = $departamento;
        if ($departamento) {
            $this->departamentoId = $departamento->getNomeId();
        }
        return $this;
    }
}

