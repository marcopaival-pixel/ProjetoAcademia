<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = [
        'name',
        'type',
        'base_url',
        'api_key',
        'secret_key',
        'timeout',
        'status',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'secret_key' => 'encrypted',
        'timeout' => 'integer',
    ];

    public static function getTypes()
    {
        return [
            'exercise' => 'Exercícios',
            'food' => 'Alimentos',
            'equipment' => 'Aparelhos',
            'ai' => 'IA',
            'health' => 'Saúde',
            'nutrition' => 'Nutrição',
        ];
    }

    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
