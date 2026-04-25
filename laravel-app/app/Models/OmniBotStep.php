<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmniBotStep extends Model
{
    protected $fillable = ['bot_id', 'label', 'type', 'content', 'is_start', 'next_step_id'];

    public function options() { return $this->hasMany(OmniBotOption::class, 'step_id'); }
}
