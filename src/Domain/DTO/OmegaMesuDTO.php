<?php

namespace App\Domain\DTO;

class OmegaMesuDTO
{
    private $segmento;
    private $segmentoId;
    private $diretoria;
    private $diretoriaId;
    private $gerenciaRegional;
    private $gerenciaRegionalId;
    private $agencia;
    private $agenciaId;
    private $gerenteGestao;
    private $gerenteGestaoId;
    private $gerente;
    private $gerenteId;

    public function __construct($segmento = null, $segmentoId = null, $diretoria = null, $diretoriaId = null, $gerenciaRegional = null, $gerenciaRegionalId = null, $agencia = null, $agenciaId = null, $gerenteGestao = null, $gerenteGestaoId = null, $gerente = null, $gerenteId = null)
    {
        $this->segmento = $segmento;
        $this->segmentoId = $segmentoId;
        $this->diretoria = $diretoria;
        $this->diretoriaId = $diretoriaId;
        $this->gerenciaRegional = $gerenciaRegional;
        $this->gerenciaRegionalId = $gerenciaRegionalId;
        $this->agencia = $agencia;
        $this->agenciaId = $agenciaId;
        $this->gerenteGestao = $gerenteGestao;
        $this->gerenteGestaoId = $gerenteGestaoId;
        $this->gerente = $gerente;
        $this->gerenteId = $gerenteId;
    }

    public function toArray()
    {
        return [
            'segmento' => $this->segmento,
            'segmento_id' => $this->segmentoId,
            'diretoria' => $this->diretoria,
            'diretoria_id' => $this->diretoriaId,
            'gerencia_regional' => $this->gerenciaRegional,
            'gerencia_regional_id' => $this->gerenciaRegionalId,
            'agencia' => $this->agencia,
            'agencia_id' => $this->agenciaId,
            'gerente_gestao' => $this->gerenteGestao,
            'gerente_gestao_id' => $this->gerenteGestaoId,
            'gerente' => $this->gerente,
            'gerente_id' => $this->gerenteId,
        ];
    }
}

