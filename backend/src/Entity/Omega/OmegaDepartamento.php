<?php

namespace App\Entity\Omega;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omega_departamentos")
 */
class OmegaDepartamento
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $departamentoId;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $departamento;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordemDepartamento;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordemTipo;

    public function getDepartamentoId(): ?string
    {
        return $this->departamentoId;
    }

    public function setDepartamentoId(string $departamentoId): self
    {
        $this->departamentoId = $departamentoId;
        return $this;
    }

    public function getDepartamento(): ?string
    {
        return $this->departamento;
    }

    public function setDepartamento(string $departamento): self
    {
        $this->departamento = $departamento;
        return $this;
    }

    public function getOrdemDepartamento(): ?int
    {
        return $this->ordemDepartamento;
    }

    public function setOrdemDepartamento(?int $ordemDepartamento): self
    {
        $this->ordemDepartamento = $ordemDepartamento;
        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getOrdemTipo(): ?int
    {
        return $this->ordemTipo;
    }

    public function setOrdemTipo(?int $ordemTipo): self
    {
        $this->ordemTipo = $ordemTipo;
        return $this;
    }
}

