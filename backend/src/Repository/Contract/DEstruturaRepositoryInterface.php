<?php

namespace App\Repository\Contract;

use App\Entity\Pobj\DEstrutura;
use App\Domain\DTO\Init\GerenteWithGestorDTO;


interface DEstruturaRepositoryInterface
{
    
    public function findByFuncional(string $funcional): ?DEstrutura;
    
    
    public function findGerentes(): array;
    
    
    public function findGerentesGestoes(): array;
    
    
    public function findGerentesWithGestor(): array;
}

