<?php

namespace App\Repository\Contract;

use App\Domain\DTO\FilterDTO;


interface RepositoryInterface
{
    
    public function findByFilters(?FilterDTO $filters = null): array;
}

