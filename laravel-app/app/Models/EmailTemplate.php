<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'empresa_id',
        'tipo',
        'nome_template',
        'assunto',
        'mensagem',
        'variaveis',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, EmailTemplate>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'empresa_id');
    }

    /**
     * @param  array<string, string>  $vars
     */
    public function renderSubject(array $vars): string
    {
        return $this->replaceVars($this->assunto, $vars);
    }

    /**
     * @param  array<string, string>  $vars
     */
    public function renderBody(array $vars): string
    {
        return $this->replaceVars($this->mensagem, $vars);
    }

    /**
     * @param  array<string, string>  $vars
     */
    private function replaceVars(string $text, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{{'.$key.'}}', (string) $value, $text);
        }

        return $text;
    }
}
