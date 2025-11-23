<?php

namespace App\Infrastructure\Persistence\Interface;

use App\Domain\DTO\FilterDTO;

interface UseCaseInterface
{
    public function handle(FilterDTO $filters = null): array;
}