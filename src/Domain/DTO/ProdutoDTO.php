<?php

namespace App\Domain\DTO;

class ProdutoDTO extends BaseFactDTO
{
    private $id;
    private $idFamilia;
    private $familia;
    private $idIndicador;
    private $indicador;
    private $idSubindicador;
    private $subindicador;
    private $peso;

    public function __construct($id = null, $idFamilia = null, $familia = null, $idIndicador = null, $indicador = null, $idSubindicador = null, $subindicador = null, $peso = null)
    {
        $this->id = $id;
        $this->idFamilia = $idFamilia;
        $this->familia = $familia;
        $this->idIndicador = $idIndicador;
        $this->indicador = $indicador;
        $this->idSubindicador = $idSubindicador;
        $this->subindicador = $subindicador;
        $this->peso = $peso;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_familia' => $this->idFamilia,
            'familia' => $this->familia,
            'familia_nome' => $this->familia,
            'id_indicador' => $this->idIndicador,
            'indicador' => $this->indicador,
            'ds_indicador' => $this->indicador,
            'id_subindicador' => $this->idSubindicador,
            'subindicador' => $this->subindicador,
            'peso' => $this->peso,
        ];
    }
}

