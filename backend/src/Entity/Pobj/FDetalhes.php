<?php

namespace App\Entity\Pobj;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="f_detalhes")
 */
class FDetalhes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    private $contratoId;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $registroId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DEstrutura")
     * @ORM\JoinColumn(name="funcional", referencedColumnName="funcional", nullable=false)
     */
    private $funcional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DProduto")
     * @ORM\JoinColumn(name="id_produto", referencedColumnName="id", nullable=false)
     */
    private $produto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="dt_cadastro", referencedColumnName="data", nullable=false)
     */
    private $dtCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="competencia", referencedColumnName="data", nullable=false)
     */
    private $competencia;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $valorMeta;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $valorRealizado;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=4, nullable=true)
     */
    private $quantidade;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=4, nullable=true)
     */
    private $peso;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=4, nullable=true)
     */
    private $pontos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="dt_vencimento", referencedColumnName="data", nullable=true)
     */
    private $dtVencimento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DCalendario")
     * @ORM\JoinColumn(name="dt_cancelamento", referencedColumnName="data", nullable=true)
     */
    private $dtCancelamento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motivoCancelamento;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $canalVenda;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tipoVenda;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $condicaoPagamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pobj\DStatusIndicador")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContratoId(): ?string
    {
        return $this->contratoId;
    }

    public function setContratoId(string $contratoId): self
    {
        $this->contratoId = $contratoId;
        return $this;
    }

    public function getRegistroId(): ?string
    {
        return $this->registroId;
    }

    public function setRegistroId(string $registroId): self
    {
        $this->registroId = $registroId;
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

    public function getDtCadastro(): ?DCalendario
    {
        return $this->dtCadastro;
    }

    public function setDtCadastro(?DCalendario $dtCadastro): self
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    public function getCompetencia(): ?DCalendario
    {
        return $this->competencia;
    }

    public function setCompetencia(?DCalendario $competencia): self
    {
        $this->competencia = $competencia;
        return $this;
    }

    public function getValorMeta(): ?string
    {
        return $this->valorMeta;
    }

    public function setValorMeta(?string $valorMeta): self
    {
        $this->valorMeta = $valorMeta;
        return $this;
    }

    public function getValorRealizado(): ?string
    {
        return $this->valorRealizado;
    }

    public function setValorRealizado(?string $valorRealizado): self
    {
        $this->valorRealizado = $valorRealizado;
        return $this;
    }

    public function getQuantidade(): ?string
    {
        return $this->quantidade;
    }

    public function setQuantidade(?string $quantidade): self
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    public function getPeso(): ?string
    {
        return $this->peso;
    }

    public function setPeso(?string $peso): self
    {
        $this->peso = $peso;
        return $this;
    }

    public function getPontos(): ?string
    {
        return $this->pontos;
    }

    public function setPontos(?string $pontos): self
    {
        $this->pontos = $pontos;
        return $this;
    }

    public function getDtVencimento(): ?DCalendario
    {
        return $this->dtVencimento;
    }

    public function setDtVencimento(?DCalendario $dtVencimento): self
    {
        $this->dtVencimento = $dtVencimento;
        return $this;
    }

    public function getDtCancelamento(): ?DCalendario
    {
        return $this->dtCancelamento;
    }

    public function setDtCancelamento(?DCalendario $dtCancelamento): self
    {
        $this->dtCancelamento = $dtCancelamento;
        return $this;
    }

    public function getMotivoCancelamento(): ?string
    {
        return $this->motivoCancelamento;
    }

    public function setMotivoCancelamento(?string $motivoCancelamento): self
    {
        $this->motivoCancelamento = $motivoCancelamento;
        return $this;
    }

    public function getCanalVenda(): ?string
    {
        return $this->canalVenda;
    }

    public function setCanalVenda(?string $canalVenda): self
    {
        $this->canalVenda = $canalVenda;
        return $this;
    }

    public function getTipoVenda(): ?string
    {
        return $this->tipoVenda;
    }

    public function setTipoVenda(?string $tipoVenda): self
    {
        $this->tipoVenda = $tipoVenda;
        return $this;
    }

    public function getCondicaoPagamento(): ?string
    {
        return $this->condicaoPagamento;
    }

    public function setCondicaoPagamento(?string $condicaoPagamento): self
    {
        $this->condicaoPagamento = $condicaoPagamento;
        return $this;
    }

    public function getStatus(): ?DStatusIndicador
    {
        return $this->status;
    }

    public function setStatus(?DStatusIndicador $status): self
    {
        $this->status = $status;
        return $this;
    }
}

