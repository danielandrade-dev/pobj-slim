<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="d_status_indicadores")
 */
class DStatusIndicador
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=20)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $status;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}

