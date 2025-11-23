<?php

namespace App\Application\UseCase;

use App\Domain\DTO\FilterDTO;
use App\Infrastructure\Persistence\FindAllMetasRepository;

/**
 * UseCase para operações relacionadas a metas
 */
class MetaUseCase
{
    /** @var FindAllMetasRepository */
    private $repository;

    /**
     * @param FindAllMetasRepository $repository
     */
    public function __construct(FindAllMetasRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todas as metas com filtros opcionais
     * @param FilterDTO|null $filters
     * @return array
     */
    public function getAllMetas(FilterDTO $filters = null): array
    {
        return $this->repository->fetch($filters);
    }
}

