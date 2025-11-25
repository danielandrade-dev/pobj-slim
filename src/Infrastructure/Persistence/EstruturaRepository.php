<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\DEstrutura;
use App\Domain\Model\Segmento;
use App\Domain\Model\Diretoria;
use App\Domain\Model\Regional;
use App\Domain\Model\Agencia;
use App\Domain\Enum\Cargo;

class EstruturaRepository
{
    /**
     * Retorna todos os segmentos (sem pai)
     * @return array
     */
    public function findAllSegmentos(): array
    {
        return Segmento::select('id', 'nome as label')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Retorna todas as diretorias com id_segmento (pai)
     * @return array
     */
    public function findAllDiretorias(): array
    {
        return Diretoria::select('id', 'nome as label', 'segmento_id as id_segmento')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Retorna todas as regionais com id_diretoria (pai)
     * @return array
     */
    public function findAllRegionais(): array
    {
        return Regional::select('id', 'nome as label', 'diretoria_id as id_diretoria')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Retorna todas as agências com id_regional (pai)
     * @return array
     */
    public function findAllAgencias(): array
    {
        return Agencia::select('id', 'nome as label', 'porte', 'regional_id as id_regional')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Retorna todos os gerentes de gestão com id_agencia (pai)
     * @return array
     */
    public function findAllGGestoes(): array
    {
        return DEstrutura::select('id','funcional', 'nome as label', 'agencia_id as id_agencia')
            ->where('cargo_id', Cargo::GERENTE_GESTAO)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->orderBy('nome', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Retorna todos os gerentes com id_gestor (pai)
     * Busca o gerente de gestão da mesma estrutura hierárquica
     * @return array
     */
    public function findGerentesWithGestor(): array
    {
        $gerentes = DEstrutura::where('cargo_id', Cargo::GERENTE)
            ->whereNotNull('funcional')
            ->whereNotNull('nome')
            ->orderBy('nome', 'ASC')
            ->get();

        return $gerentes->map(function ($gerente) {
            // Busca o gerente de gestão da mesma agência
            $gestor = null;
            if ($gerente->agencia_id) {
                $gestor = DEstrutura::where('cargo_id', Cargo::GERENTE_GESTAO)
                    ->where('agencia_id', $gerente->agencia_id)
                    ->whereNotNull('funcional')
                    ->first();
            }

            return [
                'id' => $gerente->id,
                'funcional' => $gerente->funcional,
                'label' => $gerente->nome,
                'id_gestor' => $gestor ? $gestor->id : null,
            ];
        })->toArray();
    }
}
