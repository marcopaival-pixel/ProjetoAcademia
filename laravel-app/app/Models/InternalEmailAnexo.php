<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalEmailAnexo extends Model
{
    use HasFactory;

    protected $table = 'internal_email_attachments';

    protected $fillable = [
        'mensagem_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function mensagem(): BelongsTo
    {
        return $this->belongsTo(InternalEmail::class, 'mensagem_id');
    }
}
