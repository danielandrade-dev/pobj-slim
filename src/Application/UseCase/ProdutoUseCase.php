<?php

namespace App\Application\UseCase;

use App\Infrastructure\Persistence\ProdutoRepository;

class ProdutoUseCase extends AbstractUseCase
{
    public function __construct(ProdutoRepository $repository)
    {
        parent::__construct($repository);
    }
}

