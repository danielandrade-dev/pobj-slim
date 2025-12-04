<?php

namespace App\Repository\Contract;

use App\Entity\Pobj\DEstrutura;
use App\Domain\DTO\Init\GerenteWithGestorDTO;

/**
 * Interface para DEstruturaRepository
 */
interface DEstruturaRepositoryInterface
{
    /**
     * Busca estrutura por funcional
     * 
     * @param string $funcional Código funcional
     * @return DEstrutura|null Entidade encontrada ou null
     */
    public function findByFuncional(string $funcional): ?DEstrutura;
    
    /**
     * Busca todos os gerentes
     * 
     * @return DEstrutura[] Array de gerentes
     */
    public function findGerentes(): array;
    
    /**
     * Busca todos os gerentes de gestão
     * 
     * @return DEstrutura[] Array de gerentes de gestão
     */
    public function findGerentesGestoes(): array;
    
    /**
     * Busca gerentes com seus respectivos gerentes de gestão da mesma agência
     * 
     * @return GerenteWithGestorDTO[] Array de DTOs com gerentes e gestores
     */
    public function findGerentesWithGestor(): array;
}

