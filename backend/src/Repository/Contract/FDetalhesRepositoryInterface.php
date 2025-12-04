<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;


interface FDetalhesRepositoryInterface
{
    
    public function findDetalhes(?FilterDTO $filters = null): array;
    
    
    public function countDetalhes(?FilterDTO $filters = null): int;
}

