<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'tipo_usuario',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(KnowledgeArticle::class, 'categoria_id');
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
