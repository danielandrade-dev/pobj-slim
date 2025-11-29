<?php

namespace App\Domain\DTO\Resumo;

class VariableCardDTO
{
    public $registro_id;
    public $funcional;
    public $data;
    public $competencia;
    public $variavel_real;
    public $variavel_meta;
    public $nome_funcional;
    public $segmento;
    public $segmento_id;
    public $diretoria_nome;
    public $diretoria_id;
    public $regional_nome;
    public $gerencia_id;
    public $agencia_nome;
    public $agencia_id;

    public function __construct(
        ?string $registro_id = null,
        ?string $funcional = null,
        ?string $data = null,
        ?string $competencia = null,
        float $variavel_real = 0,
        float $variavel_meta = 0,
        ?string $nome_funcional = null,
        ?string $segmento = null,
        ?string $segmento_id = null,
        ?string $diretoria_nome = null,
        ?string $diretoria_id = null,
        ?string $regional_nome = null,
        ?string $gerencia_id = null,
        ?string $agencia_nome = null,
        ?string $agencia_id = null
    ) {
        $this->registro_id = $registro_id;
        $this->funcional = $funcional;
        $this->data = $data;
        $this->competencia = $competencia;
        $this->variavel_real = $variavel_real;
        $this->variavel_meta = $variavel_meta;
        $this->nome_funcional = $nome_funcional;
        $this->segmento = $segmento;
        $this->segmento_id = $segmento_id;
        $this->diretoria_nome = $diretoria_nome;
        $this->diretoria_id = $diretoria_id;
        $this->regional_nome = $regional_nome;
        $this->gerencia_id = $gerencia_id;
        $this->agencia_nome = $agencia_nome;
        $this->agencia_id = $agencia_id;
    }

    public function toArray(): array
    {
        return [
            'registro_id' => $this->registro_id,
            'funcional' => $this->funcional,
            'data' => $this->data,
            'competencia' => $this->competencia,
            'variavel_real' => $this->variavel_real,
            'variavel_meta' => $this->variavel_meta,
            'nome_funcional' => $this->nome_funcional,
            'segmento' => $this->segmento,
            'segmento_id' => $this->segmento_id,
            'diretoria_nome' => $this->diretoria_nome,
            'diretoria_id' => $this->diretoria_id,
            'regional_nome' => $this->regional_nome,
            'gerencia_id' => $this->gerencia_id,
            'agencia_nome' => $this->agencia_nome,
            'agencia_id' => $this->agencia_id,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['registro_id']) ? (string)$data['registro_id'] : null,
            isset($data['funcional']) ? (string)$data['funcional'] : null,
            $data['data'] ?? null,
            $data['competencia'] ?? null,
            (float)($data['variavel_real'] ?? 0),
            (float)($data['variavel_meta'] ?? 0),
            $data['nome_funcional'] ?? null,
            $data['segmento'] ?? null,
            isset($data['segmento_id']) ? (string)$data['segmento_id'] : null,
            $data['diretoria_nome'] ?? null,
            isset($data['diretoria_id']) ? (string)$data['diretoria_id'] : null,
            $data['regional_nome'] ?? null,
            isset($data['gerencia_id']) ? (string)$data['gerencia_id'] : null,
            $data['agencia_nome'] ?? null,
            isset($data['agencia_id']) ? (string)$data['agencia_id'] : null
        );
    }
}

