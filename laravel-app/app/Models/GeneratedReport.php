<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GeneratedReport extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'type',
        'version',
        'hash',
        'generated_at',
        'metadata'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->document_id) {
                $model->document_id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Gera um hash seguro para o documento e versão.
     */
    public static function generateSecureHash($docId, $version, $timestamp): string
    {
        return hash_hmac('sha256', $docId . $version . $timestamp, config('app.key'));
    }
}
