<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'conteudo',
        'categoria_id',
        'tipo_usuario',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'categoria_id');
    }

    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeForUserType($query, $type)
    {
        return $query->where('tipo_usuario', $type);
    }
}
