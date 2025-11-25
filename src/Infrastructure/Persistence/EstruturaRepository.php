<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Estrutura;
use App\Domain\Enum\Cargo;

class EstruturaRepository
{
    public function findAllSegmentos(): array
    {
        return Estrutura::selectRaw('DISTINCT id_segmento AS id, segmento AS label')
            ->whereNotNull('id_segmento')
            ->whereNotNull('segmento')
            ->orderBy('segmento', 'ASC')
            ->get()
            ->toArray();
    }

    public function findAllDiretorias(): array
    {
        return Estrutura::selectRaw('DISTINCT id_diretoria AS id, diretoria AS label, id_segmento')
            ->whereNotNull('id_diretoria')
            ->whereNotNull('diretoria')
            ->orderBy('diretoria', 'ASC')
            ->get()
            ->toArray();
    }

    public function findAllRegionais(): array
    {
        return Estrutura::selectRaw('DISTINCT id_regional AS id, regional AS label, id_diretoria')
            ->whereNotNull('id_regional')
            ->whereNotNull('regional')
            ->orderBy('regional', 'ASC')
            ->get()
            ->toArray();
    }

    public function findAllAgencias(): array
    {
        return Estrutura::selectRaw('DISTINCT id_agencia AS id, agencia AS label, porte, id_regional')
            ->whereNotNull('id_agencia')
            ->whereNotNull('agencia')
            ->orderBy('agencia', 'ASC')
            ->get()
            ->toArray();
    }

    public function findAllGGestoes(): array
    {
        return Estrutura::selectRaw('DISTINCT funcional AS id, nome AS label, id_agencia')
            ->where('id_cargo', Cargo::GERENTE_GESTAO)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->where('funcional', '!=', '')
            ->where('nome', '!=', '')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    public function findAllGerentes(): array
    {
        return Estrutura::selectRaw('DISTINCT funcional AS id, nome AS label')
            ->where('id_cargo', Cargo::GERENTE)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->where('funcional', '!=', '')
            ->where('nome', '!=', '')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    public function findGGestoesForFilter(): array
    {
        return Estrutura::selectRaw('DISTINCT funcional AS id, nome AS label, cargo, id_cargo')
            ->where('id_cargo', Cargo::GERENTE_GESTAO)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    public function findGerentesForFilter(): array
    {
        return Estrutura::selectRaw('DISTINCT funcional AS id, nome AS label, cargo, id_cargo')
            ->where('id_cargo', Cargo::GERENTE)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    public function findGerentesWithGestor(): array
    {
        return Estrutura::from('d_estrutura as g')
            ->selectRaw('
                DISTINCT
                g.funcional AS id,
                g.nome AS label,
                g.agencia,
                g.id_agencia,
                g.cargo,
                g.id_cargo,
                gg.funcional AS id_gestor
            ')
            ->leftJoin('d_estrutura as gg', function ($join) {
                $join->on('gg.id_agencia', '=', 'g.id_agencia')
                     ->on('gg.id_regional', '=', 'g.id_regional')
                     ->on('gg.id_diretoria', '=', 'g.id_diretoria')
                     ->on('gg.id_segmento', '=', 'g.id_segmento')
                     ->where('gg.id_cargo', '=', Cargo::GERENTE_GESTAO);
            })
            ->where('g.id_cargo', Cargo::GERENTE)
            ->get()
            ->toArray();
    }
}
