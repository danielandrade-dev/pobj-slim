<?php

namespace App\Repository\Pobj;

use App\Entity\Pobj\DEstrutura;
use App\Domain\Enum\Cargo;
use App\Domain\DTO\Init\GerenteWithGestorDTO;
use App\Repository\Contract\DEstruturaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DEstruturaRepository extends ServiceEntityRepository implements DEstruturaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DEstrutura::class);
    }

    /**
     * Busca estrutura por funcional
     */
    public function findByFuncional(string $funcional): ?DEstrutura
    {
        return $this->createQueryBuilder('e')
                    ->andWhere('e.funcional = :funcional')
                    ->setParameter('funcional', $funcional)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function findGerentes(): array
    {
        return $this->createQueryBuilder('e')
                    ->innerJoin('e.cargo', 'c')
                    ->andWhere('c.id = :cargo_id')
                    ->setParameter('cargo_id', Cargo::GERENTE)
                    ->getQuery()
                    ->getResult();
    }
    public function findGerentesGestoes(): array
    {
        return $this->createQueryBuilder('e')
                    ->innerJoin('e.cargo', 'c')
                    ->andWhere('c.id = :cargo_id')
                    ->setParameter('cargo_id', Cargo::GERENTE_GESTAO)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Busca gerentes com seus respectivos gerentes de gestão da mesma agência
     * Otimizado para evitar N+1 queries usando eager loading e agrupamento
     * @return GerenteWithGestorDTO[]
     */
    public function findGerentesWithGestor(): array
    {
        // Busca todos os gerentes com eager loading de cargo e agencia
        $gerentes = $this->createQueryBuilder('e')
            ->innerJoin('e.cargo', 'c')
            ->leftJoin('e.agencia', 'a')
            ->addSelect('c')
            ->addSelect('a')
            ->andWhere('c.id = :cargo_id')
            ->andWhere('e.funcional IS NOT NULL')
            ->andWhere('e.nome IS NOT NULL')
            ->setParameter('cargo_id', Cargo::GERENTE)
            ->orderBy('e.nome', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($gerentes)) {
            return [];
        }

        // Agrupa gerentes por agência para buscar gestores em batch
        $agenciasIds = [];
        foreach ($gerentes as $gerente) {
            if ($gerente->getAgencia()) {
                $agenciasIds[] = $gerente->getAgencia()->getId();
            }
        }

        // Busca todos os gestores de uma vez (evita N+1)
        $gestoresPorAgencia = [];
        if (!empty($agenciasIds)) {
            $gestores = $this->createQueryBuilder('g')
                ->innerJoin('g.cargo', 'cg')
                ->innerJoin('g.agencia', 'ag')
                ->addSelect('cg')
                ->addSelect('ag')
                ->andWhere('cg.id = :cargo_gestao_id')
                ->andWhere('ag.id IN (:agencias_ids)')
                ->andWhere('g.funcional IS NOT NULL')
                ->setParameter('cargo_gestao_id', Cargo::GERENTE_GESTAO)
                ->setParameter('agencias_ids', array_unique($agenciasIds))
                ->getQuery()
                ->getResult();

            // Agrupa gestores por agência
            foreach ($gestores as $gestor) {
                if ($gestor->getAgencia()) {
                    $agenciaId = $gestor->getAgencia()->getId();
                    if (!isset($gestoresPorAgencia[$agenciaId])) {
                        $gestoresPorAgencia[$agenciaId] = $gestor;
                    }
                }
            }
        }

        // Monta o resultado usando o cache de gestores
        $result = [];
        foreach ($gerentes as $gerente) {
            $gestor = null;
            if ($gerente->getAgencia()) {
                $agenciaId = $gerente->getAgencia()->getId();
                $gestor = $gestoresPorAgencia[$agenciaId] ?? null;
            }

            $result[] = new GerenteWithGestorDTO(
                $gerente->getId(),
                $gerente->getFuncional(),
                $gerente->getNome(),
                $gestor ? $gestor->getId() : null
            );
        }

        return $result;
    }
}

