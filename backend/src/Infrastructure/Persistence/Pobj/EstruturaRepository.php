<?php

namespace App\Infrastructure\Persistence\Pobj;

use App\Domain\Enum\Cargo;
use App\Domain\Enum\StatusIndicador;
use App\Infrastructure\Database\Connection;

class EstruturaRepository
{
    private $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    /**
     * Retorna todos os segmentos (sem pai)
     * @return array
     */
    public function findAllSegmentos(): array
    {
        $sql = "SELECT id, nome as label FROM segmentos ORDER BY nome ASC";
        return $this->connection->select($sql);
    }

    /**
     * Retorna todas as diretorias com id_segmento (pai)
     * @return array
     */
    public function findAllDiretorias(): array
    {
        $sql = "SELECT id, nome as label, segmento_id as id_segmento FROM diretorias ORDER BY nome ASC";
        return $this->connection->select($sql);
    }

    /**
     * Retorna todas as regionais com id_diretoria (pai)
     * @return array
     */
    public function findAllRegionais(): array
    {
        $sql = "SELECT id, nome as label, diretoria_id as id_diretoria FROM regionais ORDER BY nome ASC";
        return $this->connection->select($sql);
    }

    /**
     * Retorna todas as agências com id_regional (pai)
     * @return array
     */
    public function findAllAgencias(): array
    {
        $sql = "SELECT id, nome as label, porte, regional_id as id_regional FROM agencias ORDER BY nome ASC";
        return $this->connection->select($sql);
    }

    /**
     * Retorna todos os gerentes de gestão com id_agencia (pai)
     * @return array
     */
    public function findAllGGestoes(): array
    {
        $sql = "SELECT id, funcional, nome as label, agencia_id as id_agencia 
                FROM d_estrutura 
                WHERE cargo_id = ? 
                AND funcional IS NOT NULL 
                AND nome IS NOT NULL 
                ORDER BY nome ASC";
        return $this->connection->select($sql, [Cargo::GERENTE_GESTAO]);
    }

    /**
     * Retorna todos os gerentes com id_gestor (pai)
     * Busca o gerente de gestão da mesma estrutura hierárquica
     * @return array
     */
    public function findGerentesWithGestor(): array
    {
        $sql = "SELECT id, funcional, nome, agencia_id 
                FROM d_estrutura 
                WHERE cargo_id = ? 
                AND funcional IS NOT NULL 
                AND nome IS NOT NULL 
                ORDER BY nome ASC";
        
        $gerentes = $this->connection->select($sql, [Cargo::GERENTE]);
        
        $result = [];
        foreach ($gerentes as $gerente) {
            // Busca o gerente de gestão da mesma agência
            $gestor = null;
            if ($gerente['agencia_id']) {
                $gestorSql = "SELECT id FROM d_estrutura 
                              WHERE cargo_id = ? 
                              AND agencia_id = ? 
                              AND funcional IS NOT NULL 
                              LIMIT 1";
                $gestorResult = $this->connection->select($gestorSql, [
                    Cargo::GERENTE_GESTAO,
                    $gerente['agencia_id']
                ]);
                $gestor = !empty($gestorResult) ? $gestorResult[0] : null;
            }

            $result[] = [
                'id' => $gerente['id'],
                'funcional' => $gerente['funcional'],
                'label' => $gerente['nome'],
                'id_gestor' => $gestor ? $gestor['id'] : null,
            ];
        }
        
        return $result;
    }

    /**
     * Retorna todas as famílias (sem pai)
     * @return array
     */
    public function findAllFamilias(): array
    {
        $sql = "SELECT id, nm_familia as label FROM familia ORDER BY nm_familia ASC";
        return $this->connection->select($sql);
    }

    /**
     * Retorna todos os indicadores com familia (pai) junto
     * @return array
     */
    public function findAllIndicadores(): array
    {
        $sql = "SELECT i.id, i.nm_indicador as label, i.familia_id 
                FROM indicador i 
                INNER JOIN familia f ON f.id = i.familia_id
                ORDER BY i.nm_indicador ASC";
        
        $indicadores = $this->connection->select($sql);
        
        $result = [];
        foreach ($indicadores as $indicador) {
            $result[] = [
                'id' => $indicador['id'],
                'label' => $indicador['label'],
                'familia_id' => $indicador['familia_id'],
            ];
        }
        
        return $result;
    }

    /**
     * Retorna todos os subindicadores com indicador (pai) junto
     * @return array
     */
    public function findAllSubindicadores(): array
    {
        $sql = "SELECT s.id, s.nm_subindicador as label, s.indicador_id 
                FROM subindicador s 
                INNER JOIN indicador i ON i.id = s.indicador_id
                ORDER BY s.nm_subindicador ASC";
        
        $subindicadores = $this->connection->select($sql);
        
        $result = [];
        foreach ($subindicadores as $subindicador) {
            $result[] = [
                'id' => $subindicador['id'],
                'label' => $subindicador['label'],
                'indicador_id' => $subindicador['indicador_id'],
            ];
        }
        
        return $result;
    }

    /**
     * Retorna todos os status de indicadores
     * @return array
     */
    public function findAllStatusIndicadores(): array
    {
        $sql = "SELECT id, status as label FROM d_status_indicadores ORDER BY id ASC";
        $status = $this->connection->select($sql);

        // Se não houver resultados, retorna os defaults
        if (empty($status)) {
            return StatusIndicador::getDefaults();
        }

        $result = [];
        foreach ($status as $item) {
            $result[] = [
                'id' => $item['id'],
                'label' => $item['label'],
            ];
        }
        
        return $result;
    }

    /**
     * Retorna gerentes de gestão para filtros
     * @return array
     */
    public function findGGestoesForFilter(): array
    {
        return $this->findAllGGestoes();
    }

    /**
     * Retorna gerentes para filtros
     * @return array
     */
    public function findGerentesForFilter(): array
    {
        return $this->findGerentesWithGestor();
    }
}

