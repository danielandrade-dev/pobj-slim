<?php

namespace App\Application\UseCase\Pobj;

use App\Repository\Pobj\SegmentoRepository;
use App\Repository\Pobj\DiretoriaRepository;
use App\Repository\Pobj\RegionalRepository;
use App\Repository\Pobj\AgenciaRepository;
use App\Repository\Pobj\DEstruturaRepository;
use App\Repository\Pobj\FamiliaRepository;
use App\Repository\Pobj\IndicadorRepository;
use App\Repository\Pobj\SubindicadorRepository;
use App\Repository\Pobj\DStatusIndicadorRepository;

class EstruturaUseCase
{
    private $segmentoRepository;
    private $diretoriaRepository;
    private $regionalRepository;
    private $agenciaRepository;
    private $estruturaRepository;
    private $familiaRepository;
    private $indicadorRepository;
    private $subindicadorRepository;
    private $statusRepository;

    public function __construct(
        SegmentoRepository $segmentoRepository,
        DiretoriaRepository $diretoriaRepository,
        RegionalRepository $regionalRepository,
        AgenciaRepository $agenciaRepository,
        DEstruturaRepository $estruturaRepository,
        FamiliaRepository $familiaRepository,
        IndicadorRepository $indicadorRepository,
        SubindicadorRepository $subindicadorRepository,
        DStatusIndicadorRepository $statusRepository
    ) {
        $this->segmentoRepository = $segmentoRepository;
        $this->diretoriaRepository = $diretoriaRepository;
        $this->regionalRepository = $regionalRepository;
        $this->agenciaRepository = $agenciaRepository;
        $this->estruturaRepository = $estruturaRepository;
        $this->familiaRepository = $familiaRepository;
        $this->indicadorRepository = $indicadorRepository;
        $this->subindicadorRepository = $subindicadorRepository;
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

    public function handle(): array
    {
        return [
            'segmentos'       => $this->formatEntityToArray($this->segmentoRepository->findAllOrderedByNome()),
            'diretorias'      => $this->formatEntityToArray($this->diretoriaRepository->findAllOrderedByNome()),
            'regionais'       => $this->formatEntityToArray($this->regionalRepository->findAllOrderedByNome()),
            'agencias'        => $this->formatEntityToArray($this->agenciaRepository->findAllOrderedByNome()),
            'gerentes_gestao' => [], // TODO: Implementar com DEstruturaRepository
            'gerentes'        => [], // TODO: Implementar com DEstruturaRepository
            'familias'        => $this->formatEntityToArray($this->familiaRepository->findAllOrderedByNome(), 'id', 'nmFamilia'),
            'indicadores'     => $this->formatEntityToArray($this->indicadorRepository->findAllOrderedByNome(), 'id', 'nmIndicador'),
            'subindicadores'  => $this->formatEntityToArray($this->subindicadorRepository->findAllOrderedByNome(), 'id', 'nmSubindicador'),
            'status_indicadores' => $this->formatEntityToArray($this->statusRepository->findAllOrderedById(), 'id', 'status'),
        ];
    }
}

