<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleMenuPermission extends Model
{
    use BelongsToCompany;
    protected $table = 'role_menu_permissions';

    protected $fillable = [
        'role_id',
        'menu_id',
        'pode_visualizar',
        'pode_criar',
        'pode_editar',
        'pode_excluir',
        'pode_exportar',
        'pode_imprimir',
        'academy_company_id',
    ];

    protected function casts(): array
    {
        return [
            'pode_visualizar' => 'boolean',
            'pode_criar' => 'boolean',
            'pode_editar' => 'boolean',
            'pode_excluir' => 'boolean',
            'pode_exportar' => 'boolean',
            'pode_imprimir' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Profile, RoleMenuPermission>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'role_id');
    }

    /**
     * @return BelongsTo<Menu, RoleMenuPermission>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsTo<AcademyCompany, RoleMenuPermission>
     */
    public function academyCompany(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class);
    }
}
