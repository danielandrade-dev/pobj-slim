<?php

namespace App\Entity\Omega;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omega_usuarios")
 */
class OmegaUsuario
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=40)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $funcional;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $cargo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $usuario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $analista;

    /**
     * @ORM\Column(type="boolean")
     */
    private $supervisor;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $encarteiramento;

    /**
     * @ORM\Column(type="boolean")
     */
    private $meta;

    /**
     * @ORM\Column(type="boolean")
     */
    private $orcamento;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pobj;

    /**
     * @ORM\Column(type="boolean")
     */
    private $matriz;

    /**
     * @ORM\Column(type="boolean")
     */
    private $outros;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
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

    public function getFuncional(): ?string
    {
        return $this->funcional;
    }

    public function setFuncional(?string $funcional): self
    {
        $this->funcional = $funcional;
        return $this;
    }

    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    public function setCargo(?string $cargo): self
    {
        $this->cargo = $cargo;
        return $this;
    }

    public function getUsuario(): ?bool
    {
        return $this->usuario;
    }

    public function setUsuario(bool $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getAnalista(): ?bool
    {
        return $this->analista;
    }

    public function setAnalista(bool $analista): self
    {
        $this->analista = $analista;
        return $this;
    }

    public function getSupervisor(): ?bool
    {
        return $this->supervisor;
    }

    public function setSupervisor(bool $supervisor): self
    {
        $this->supervisor = $supervisor;
        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;
        return $this;
    }

    public function getEncarteiramento(): ?bool
    {
        return $this->encarteiramento;
    }

    public function setEncarteiramento(bool $encarteiramento): self
    {
        $this->encarteiramento = $encarteiramento;
        return $this;
    }

    public function getMeta(): ?bool
    {
        return $this->meta;
    }

    public function setMeta(bool $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getOrcamento(): ?bool
    {
        return $this->orcamento;
    }

    public function setOrcamento(bool $orcamento): self
    {
        $this->orcamento = $orcamento;
        return $this;
    }

    public function getPobj(): ?bool
    {
        return $this->pobj;
    }

    public function setPobj(bool $pobj): self
    {
        $this->pobj = $pobj;
        return $this;
    }

    public function getMatriz(): ?bool
    {
        return $this->matriz;
    }

    public function setMatriz(bool $matriz): self
    {
        $this->matriz = $matriz;
        return $this;
    }

    public function getOutros(): ?bool
    {
        return $this->outros;
    }

    public function setOutros(bool $outros): self
    {
        $this->outros = $outros;
        return $this;
    }
}

