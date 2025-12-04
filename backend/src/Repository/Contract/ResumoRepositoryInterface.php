<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;


interface ResumoRepositoryInterface
{
    
    public function findProdutos(?FilterDTO $filters = null): array;
    
    
    public function findProdutosMensais(?FilterDTO $filters = null): array;
    
    
    public function findVariavel(?FilterDTO $filters = null): array;
    
    
    public function findCalendario(): array;
}

