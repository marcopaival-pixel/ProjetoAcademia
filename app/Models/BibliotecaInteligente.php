<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibliotecaInteligente extends Model
{
    protected $table = 'biblioteca_inteligente';

    protected $fillable = [
        'modulo',
        'categoria',
        'tipo_item',
        'titulo',
        'descricao',
        'pergunta',
        'palavras_chave',
        'conteudo',
        'origem',
        'visibilidade',
        'status',
        'versao',
        'uso_count',
        'created_by',
        'parent_id',
        'ativo',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(BibliotecaInteligente::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BibliotecaInteligente::class, 'parent_id');
    }
}
