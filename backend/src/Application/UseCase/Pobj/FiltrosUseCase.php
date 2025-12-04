<?php

namespace App\Application\UseCase\Pobj;

use App\Domain\ValueObject\FiltroNivel;
use App\Repository\Pobj\SegmentoRepository;
use App\Repository\Pobj\DiretoriaRepository;
use App\Repository\Pobj\RegionalRepository;
use App\Repository\Pobj\AgenciaRepository;
use App\Repository\Pobj\DStatusIndicadorRepository;

class FiltrosUseCase
{
    private $segmentoRepository;
    private $diretoriaRepository;
    private $regionalRepository;
    private $agenciaRepository;
    private $statusRepository;

    public function __construct(
        SegmentoRepository $segmentoRepository,
        DiretoriaRepository $diretoriaRepository,
        RegionalRepository $regionalRepository,
        AgenciaRepository $agenciaRepository,
        DStatusIndicadorRepository $statusRepository
    ) {
        $this->segmentoRepository = $segmentoRepository;
        $this->diretoriaRepository = $diretoriaRepository;
        $this->regionalRepository = $regionalRepository;
        $this->agenciaRepository = $agenciaRepository;
        $this->statusRepository = $statusRepository;
    }

    private function formatEntityToArray($entities, $idField = 'id', $labelField = 'nome'): array
    {
        $result = [];
        foreach ($entities as $entity) {
            $getterId = 'get' . ucfirst($idField);
            $getterLabel = 'get' . ucfirst($labelField);
            $result[] = [
                'id' => method_exists($entity, $getterId) ? $entity->$getterId() : null,
                'label' => method_exists($entity, $getterLabel) ? $entity->$getterLabel() : null,
            ];
        }
        return $result;
    }

    public function getFiltroByNivel($nivel): array
    {
        // Compatibilidade com PHP 7.1 - aceita string
        $nivelValue = is_string($nivel) ? $nivel : (string)$nivel;
        
        switch ($nivelValue) {
            case FiltroNivel::SEGMENTOS:
                return $this->formatEntityToArray($this->segmentoRepository->findAllOrderedByNome());
            case FiltroNivel::DIRETORIAS:
                return $this->formatEntityToArray($this->diretoriaRepository->findAllOrderedByNome());
            case FiltroNivel::REGIONAIS:
                return $this->formatEntityToArray($this->regionalRepository->findAllOrderedByNome());
            case FiltroNivel::AGENCIAS:
                return $this->formatEntityToArray($this->agenciaRepository->findAllOrderedByNome());
            case FiltroNivel::GGESTOES:
                return []; // TODO: Implementar com DEstruturaRepository
            case FiltroNivel::GERENTES:
                return []; // TODO: Implementar com DEstruturaRepository
            case FiltroNivel::STATUS_INDICADORES:
                return $this->formatEntityToArray($this->statusRepository->findAllOrderedById(), 'id', 'status');
            default:
                throw new \InvalidArgumentException('Nível inválido: ' . $nivelValue);
        }
    }
}

