<?php

namespace App\Infrastructure\Persistence;

use PDO;
use App\Domain\Enum\Cargo;

class EstruturaRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllSegmentos(): array
    {
        $sql = "SELECT DISTINCT id_segmento AS id, segmento AS label
                FROM d_estrutura
                WHERE id_segmento IS NOT NULL
                  AND segmento IS NOT NULL
                ORDER BY segmento ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllDiretorias(): array
    {
        $sql = "SELECT DISTINCT id_diretoria AS id, diretoria AS label
                FROM d_estrutura
                WHERE id_diretoria IS NOT NULL
                  AND diretoria IS NOT NULL
                ORDER BY diretoria ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllRegionais(): array
    {
        $sql = "SELECT DISTINCT id_regional AS id, regional AS label
                FROM d_estrutura
                WHERE id_regional IS NOT NULL
                  AND regional IS NOT NULL
                ORDER BY regional ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllAgencias(): array
    {
        $sql = "SELECT DISTINCT id_agencia AS id, agencia AS label, porte
                FROM d_estrutura
                WHERE id_agencia IS NOT NULL
                  AND agencia IS NOT NULL
                ORDER BY agencia ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllGGestoes(): array
    {
        $sql = "SELECT DISTINCT funcional AS id, nome AS label
                FROM d_estrutura
                WHERE id_cargo = :idCargo
                  AND funcional IS NOT NULL
                  AND nome IS NOT NULL
                  AND funcional != ''
                  AND nome != ''
                ORDER BY nome ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCargo' => Cargo::GERENTE_GESTAO]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllGerentes(): array
    {
        $sql = "SELECT DISTINCT funcional AS id, nome AS label
                FROM d_estrutura
                WHERE id_cargo = :idCargo
                  AND funcional IS NOT NULL
                  AND nome IS NOT NULL
                  AND funcional != ''
                  AND nome != ''
                ORDER BY nome ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCargo' => Cargo::GERENTE]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findGGestoesForFilter(): array
    {
        $sql = "SELECT DISTINCT funcional AS id, nome AS label, cargo, id_cargo
                FROM d_estrutura
                WHERE id_cargo = :idCargo
                  AND funcional IS NOT NULL
                  AND nome IS NOT NULL
                ORDER BY nome ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCargo' => Cargo::GERENTE_GESTAO]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findGerentesForFilter(): array
    {
        $sql = "SELECT DISTINCT funcional AS id, nome AS label, cargo, id_cargo
                FROM d_estrutura
                WHERE id_cargo = :idCargo
                  AND funcional IS NOT NULL
                  AND nome IS NOT NULL
                ORDER BY nome ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCargo' => Cargo::GERENTE]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
