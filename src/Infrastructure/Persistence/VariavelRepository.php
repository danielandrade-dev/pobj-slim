<?php

namespace App\Infrastructure\Persistence;

// Removed Connection dependency
use PDO;

class VariavelRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllAsArray(): array
    {
        $sql = "SELECT 
                    v.id,
                    v.funcional,
                    v.meta,
                    v.variavel,
                    v.dt_atualizacao,
                    COALESCE(e.nome, '') AS nome_funcional,
                    COALESCE(e.segmento, '') AS segmento,
                    COALESCE(CAST(e.id_segmento AS CHAR), '') AS segmento_id,
                    COALESCE(e.diretoria, '') AS diretoria_nome,
                    COALESCE(CAST(e.id_diretoria AS CHAR), '') AS diretoria_id,
                    COALESCE(e.regional, '') AS regional_nome,
                    COALESCE(CAST(e.id_regional AS CHAR), '') AS gerencia_id,
                    COALESCE(e.agencia, '') AS agencia_nome,
                    COALESCE(CAST(e.id_agencia AS CHAR), '') AS agencia_id
                FROM f_variavel v
                LEFT JOIN d_estrutura e ON e.funcional = CAST(v.funcional AS CHAR)
                ORDER BY v.dt_atualizacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

