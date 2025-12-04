<?php

namespace App\Entity\Omega;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omega_chamados")
 */
class OmegaChamado
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=60)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $productLabel;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $family;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $section;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $queue;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Omega\OmegaStatus")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $priority;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $opened;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Omega\OmegaUsuario")
     * @ORM\JoinColumn(name="requester_id", referencedColumnName="id", nullable=true)
     */
    private $requester;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Omega\OmegaUsuario")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Omega\OmegaDepartamento")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="nome_id", nullable=true)
     */
    private $team;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $history;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $diretoria;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gerencia;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $agencia;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gerenteGestao;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gerente;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $credit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachment;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductLabel(): ?string
    {
        return $this->productLabel;
    }

    public function setProductLabel(?string $productLabel): self
    {
        $this->productLabel = $productLabel;
        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;
        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(?string $section): self
    {
        $this->section = $section;
        return $this;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function setQueue(?string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getStatus(): ?OmegaStatus
    {
        return $this->status;
    }

    public function setStatus(?OmegaStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getOpened(): ?\DateTimeInterface
    {
        return $this->opened;
    }

    public function setOpened(?\DateTimeInterface $opened): self
    {
        $this->opened = $opened;
        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;
        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getRequester(): ?OmegaUsuario
    {
        return $this->requester;
    }

    public function setRequester(?OmegaUsuario $requester): self
    {
        $this->requester = $requester;
        return $this;
    }

    public function getOwner(): ?OmegaUsuario
    {
        return $this->owner;
    }

    public function setOwner(?OmegaUsuario $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getTeam(): ?OmegaDepartamento
    {
        return $this->team;
    }

    public function setTeam(?OmegaDepartamento $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getHistory(): ?string
    {
        return $this->history;
    }

    public function setHistory(?string $history): self
    {
        $this->history = $history;
        return $this;
    }

    public function getDiretoria(): ?string
    {
        return $this->diretoria;
    }

    public function setDiretoria(?string $diretoria): self
    {
        $this->diretoria = $diretoria;
        return $this;
    }

    public function getGerencia(): ?string
    {
        return $this->gerencia;
    }

    public function setGerencia(?string $gerencia): self
    {
        $this->gerencia = $gerencia;
        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(?string $agencia): self
    {
        $this->agencia = $agencia;
        return $this;
    }

    public function getGerenteGestao(): ?string
    {
        return $this->gerenteGestao;
    }

    public function setGerenteGestao(?string $gerenteGestao): self
    {
        $this->gerenteGestao = $gerenteGestao;
        return $this;
    }

    public function getGerente(): ?string
    {
        return $this->gerente;
    }

    public function setGerente(?string $gerente): self
    {
        $this->gerente = $gerente;
        return $this;
    }

    public function getCredit(): ?string
    {
        return $this->credit;
    }

    public function setCredit(?string $credit): self
    {
        $this->credit = $credit;
        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;
        return $this;
    }
}

