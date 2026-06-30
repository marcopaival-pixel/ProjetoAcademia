<?php

namespace Database\Seeders;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopCoupon;
use App\Models\ShopProduct;
use App\Models\ShopSupplier;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ShopHomologSeeder extends Seeder
{
    /**
     * Catálogo demo para homologação local — idempotente por slug.
     */
    public function run(): void
    {
        $this->call(ShopBaseSeeder::class);

        SystemSetting::set('pagamento_ativo', 'false');
        SystemSetting::set('shop_points_per_real', '100');
        SystemSetting::set('shop_cashback_percent', '5');

        $adminId = User::query()->where('is_admin', true)->orderBy('id')->value('id')
            ?? User::query()->orderBy('id')->value('id');

        $companies = AcademyCompany::all();

        foreach ($companies as $company) {
            $vendor = ShopVendor::firstOrCreate(
                ['academy_company_id' => $company->id, 'slug' => 'academia-propria'],
                [
                    'name' => $company->name ?? 'Academia Própria',
                    'commission_rate' => 0,
                    'status' => ShopVendor::STATUS_ACTIVE,
                    'approved_at' => now(),
                ]
            );

            $partnerVendor = ShopVendor::firstOrCreate(
                ['academy_company_id' => $company->id, 'slug' => 'parceiro-demo'],
                [
                    'name' => 'Parceiro Demo',
                    'email' => 'parceiro-demo@example.local',
                    'commission_rate' => 10,
                    'status' => ShopVendor::STATUS_ACTIVE,
                    'approved_at' => now(),
                ]
            );

            $category = ShopCategory::firstOrCreate(
                ['academy_company_id' => $company->id, 'slug' => 'suplementos'],
                [
                    'name' => 'Suplementos',
                    'icon' => 'flask',
                    'product_type' => 'physical',
                    'sort_order' => 0,
                    'is_active' => true,
                ]
            );

            $supplier = ShopSupplier::firstOrCreate(
                ['academy_company_id' => $company->id, 'name' => 'Distribuidora Demo'],
                [
                    'contact_name' => 'Equipe Compras',
                    'email' => 'compras-demo@example.local',
                    'is_active' => true,
                ]
            );

            $products = [
                [
                    'slug' => 'whey-protein-demo',
                    'name' => 'Whey Protein 900g (Demo)',
                    'price' => 149.90,
                    'sale_price' => 129.90,
                    'vendor_id' => $vendor->id,
                    'supplier_id' => $supplier->id,
                    'goal_types' => ['hipertrofia'],
                    'ai_tags' => ['whey', 'proteina', 'hipertrofia'],
                    'is_featured' => true,
                    'stock_quantity' => 50,
                ],
                [
                    'slug' => 'creatina-demo',
                    'name' => 'Creatina 300g (Demo)',
                    'price' => 89.90,
                    'sale_price' => null,
                    'vendor_id' => $vendor->id,
                    'supplier_id' => $supplier->id,
                    'goal_types' => ['hipertrofia', 'performance'],
                    'is_featured' => true,
                    'stock_quantity' => 80,
                ],
                [
                    'slug' => 'shake-parceiro-demo',
                    'name' => 'Shake Proteico Parceiro (Demo)',
                    'price' => 59.90,
                    'sale_price' => null,
                    'vendor_id' => $partnerVendor->id,
                    'is_featured' => false,
                    'stock_quantity' => 30,
                ],
                [
                    'slug' => 'ebook-treino-demo',
                    'name' => 'E-book Treino em Casa (Demo)',
                    'price' => 29.90,
                    'sale_price' => null,
                    'vendor_id' => $vendor->id,
                    'is_featured' => false,
                    'type' => ShopProduct::TYPE_DIGITAL,
                    'manage_stock' => false,
                    'stock_quantity' => 0,
                    'downloadable_file' => 'shop/downloads/ebook-treino-demo.pdf',
                    'download_limit' => 5,
                    'download_expiry_days' => 30,
                ],
            ];

            Storage::disk('local')->put('shop/downloads/ebook-treino-demo.pdf', 'E-book demo homologação');

            foreach ($products as $data) {
                ShopProduct::firstOrCreate(
                    [
                        'academy_company_id' => $company->id,
                        'slug' => $data['slug'],
                    ],
                    [
                        'vendor_id' => $data['vendor_id'],
                        'category_id' => $category->id,
                        'supplier_id' => $data['supplier_id'] ?? null,
                        'goal_types' => $data['goal_types'] ?? null,
                        'ai_tags' => $data['ai_tags'] ?? null,
                        'type' => $data['type'] ?? ShopProduct::TYPE_PHYSICAL,
                        'name' => $data['name'],
                        'short_description' => 'Produto de demonstração para homologação.',
                        'price' => $data['price'],
                        'sale_price' => $data['sale_price'],
                        'manage_stock' => $data['manage_stock'] ?? true,
                        'stock_quantity' => $data['stock_quantity'],
                        'downloadable_file' => $data['downloadable_file'] ?? null,
                        'download_limit' => $data['download_limit'] ?? null,
                        'download_expiry_days' => $data['download_expiry_days'] ?? null,
                        'is_featured' => $data['is_featured'],
                        'is_active' => true,
                        'status' => ShopProduct::STATUS_PUBLISHED,
                        'published_at' => now()->subDays(3),
                    ]
                );
            }

            if ($adminId !== null) {
                ShopCoupon::firstOrCreate(
                    [
                        'academy_company_id' => $company->id,
                        'code' => 'DEMO10',
                    ],
                    [
                        'created_by' => $adminId,
                        'description' => 'Cupom demo — 10% de desconto',
                        'type' => ShopCoupon::TYPE_PERCENTAGE,
                        'discount_value' => 10,
                        'minimum_order_value' => 50,
                        'max_uses_total' => 1000,
                        'max_uses_per_user' => 5,
                        'status' => 'active',
                        'starts_at' => now()->subDay(),
                        'expires_at' => now()->addYear(),
                    ]
                );

                ShopCoupon::firstOrCreate(
                    [
                        'academy_company_id' => $company->id,
                        'code' => 'FRETEGRATIS',
                    ],
                    [
                        'created_by' => $adminId,
                        'description' => 'Cupom demo — frete grátis',
                        'type' => ShopCoupon::TYPE_FREE_SHIPPING,
                        'free_shipping' => true,
                        'status' => 'active',
                        'expires_at' => now()->addYear(),
                    ]
                );
            }
        }

        if ($adminId === null) {
            $this->command?->warn('ShopHomologSeeder: cupons demo ignorados (sem utilizador).');
        }

        $this->command?->info('ShopHomologSeeder: catálogo demo, cupons e settings aplicados.');
    }
}
