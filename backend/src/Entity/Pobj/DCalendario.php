<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="d_calendario")
 */
class DCalendario
{
    /**
     * @ORM\Id
     * @ORM\Column(type="date", nullable=false)
     */
    private $data;

    /**
     * @ORM\Column(type="integer")
     */
    private $ano;

    /**
     * @ORM\Column(type="smallint")
     */
    private $mes;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $mesNome;

    /**
     * @ORM\Column(type="smallint")
     */
    private $dia;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $diaDaSemana;

    /**
     * @ORM\Column(type="smallint")
     */
    private $semana;

    /**
     * @ORM\Column(type="smallint")
     */
    private $trimestre;

    /**
     * @ORM\Column(type="smallint")
     */
    private $semestre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ehDiaUtil;

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getAno(): ?int
    {
        return $this->ano;
    }

    public function setAno(int $ano): self
    {
        $this->ano = $ano;
        return $this;
    }

    public function getMes(): ?int
    {
        return $this->mes;
    }

    public function setMes(int $mes): self
    {
        $this->mes = $mes;
        return $this;
    }

    public function getMesNome(): ?string
    {
        return $this->mesNome;
    }

    public function setMesNome(string $mesNome): self
    {
        $this->mesNome = $mesNome;
        return $this;
    }

    public function getDia(): ?int
    {
        return $this->dia;
    }

    public function setDia(int $dia): self
    {
        $this->dia = $dia;
        return $this;
    }

    public function getDiaDaSemana(): ?string
    {
        return $this->diaDaSemana;
    }

    public function setDiaDaSemana(string $diaDaSemana): self
    {
        $this->diaDaSemana = $diaDaSemana;
        return $this;
    }

    public function getSemana(): ?int
    {
        return $this->semana;
    }

    public function setSemana(int $semana): self
    {
        $this->semana = $semana;
        return $this;
    }

    public function getTrimestre(): ?int
    {
        return $this->trimestre;
    }

    public function setTrimestre(int $trimestre): self
    {
        $this->trimestre = $trimestre;
        return $this;
    }

    public function getSemestre(): ?int
    {
        return $this->semestre;
    }

    public function setSemestre(int $semestre): self
    {
        $this->semestre = $semestre;
        return $this;
    }

    public function getEhDiaUtil(): ?bool
    {
        return $this->ehDiaUtil;
    }

    public function setEhDiaUtil(bool $ehDiaUtil): self
    {
        $this->ehDiaUtil = $ehDiaUtil;
        return $this;
    }
}

