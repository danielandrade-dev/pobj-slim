<?php

namespace App\Domain\Model;

use Illuminate\Database\Eloquent\Model;

class Estrutura extends Model
{
    protected $table = 'd_estrutura';
    
    protected $primaryKey = 'funcional';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'funcional',
        'nome',
        'cargo',
        'id_cargo',
        'agencia',
        'id_agencia',
        'porte',
        'regional',
        'id_regional',
        'diretoria',
        'id_diretoria',
        'segmento',
        'id_segmento',
        'rede',
        'created_at',
    ];
    
    protected $casts = [
        'id_cargo' => 'integer',
        'id_agencia' => 'integer',
        'id_regional' => 'integer',
        'id_diretoria' => 'integer',
        'id_segmento' => 'integer',
        'created_at' => 'datetime',
    ];
}

