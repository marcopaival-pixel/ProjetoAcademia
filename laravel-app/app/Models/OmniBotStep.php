<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmniBotStep extends Model
{
    protected $fillable = ['bot_id', 'label', 'type', 'content', 'is_start', 'next_step_id'];

    public function options(): HasMany
    {
        return $this->hasMany(OmniBotOption::class, 'step_id');
    }
}
