<?php

namespace Database\Seeders;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopVendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopBaseSeeder extends Seeder
{
    /**
     * Cria o vendor padrão "Academia Própria" (id=1 por convenção) e as categorias
     * iniciais para cada AcademyCompany existente.
     *
     * Idempotente: usa firstOrCreate para não duplicar em re-execuções.
     */
    public function run(): void
    {
        $companies = AcademyCompany::all();

        foreach ($companies as $company) {
            // ── Vendor padrão ──────────────────────────────────────────────
            ShopVendor::firstOrCreate(
                [
                    'academy_company_id' => $company->id,
                    'slug'               => 'academia-propria',
                ],
                [
                    'name'            => $company->name ?? 'Academia Própria',
                    'email'           => null,
                    'commission_rate' => 0, // academia retém 100% dos seus próprios produtos
                    'status'          => ShopVendor::STATUS_ACTIVE,
                    'approved_at'     => now(),
                ]
            );

            // ── Categorias iniciais ────────────────────────────────────────
            $categories = [
                ['name' => 'Suplementos',              'icon' => 'flask',       'product_type' => 'physical'],
                ['name' => 'Vitaminas',                'icon' => 'capsules',    'product_type' => 'physical'],
                ['name' => 'Roupas Esportivas',        'icon' => 'shirt',       'product_type' => 'physical'],
                ['name' => 'Acessórios',               'icon' => 'dumbbell',    'product_type' => 'physical'],
                ['name' => 'Equipamentos de Treino',   'icon' => 'bicycle',     'product_type' => 'physical'],
                ['name' => 'Alimentação Saudável',     'icon' => 'leaf',        'product_type' => 'physical'],
                ['name' => 'Produtos Digitais',        'icon' => 'download',    'product_type' => 'digital'],
                ['name' => 'Serviços da Academia',     'icon' => 'calendar',    'product_type' => 'service'],
                ['name' => 'Produtos Personalizados',  'icon' => 'star',        'product_type' => 'physical'],
            ];

            foreach ($categories as $i => $cat) {
                ShopCategory::firstOrCreate(
                    [
                        'academy_company_id' => $company->id,
                        'slug'               => Str::slug($cat['name']),
                    ],
                    [
                        'name'         => $cat['name'],
                        'icon'         => $cat['icon'],
                        'product_type' => $cat['product_type'],
                        'sort_order'   => $i,
                        'is_active'    => true,
                    ]
                );
            }
        }

        $this->command->info('ShopBaseSeeder: vendors e categorias criados com sucesso.');
    }
}
