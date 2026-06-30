<?php

use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['name' => 'admin_nav_shop', 'label' => 'Shopping Fitness', 'route' => 'admin.shop.products.*', 'match_mode' => 'pattern', 'order' => 325],
            ['name' => 'admin_nav_shop_orders', 'label' => 'Pedidos Shopping', 'route' => 'admin.shop.orders.*', 'match_mode' => 'pattern', 'order' => 326],
            ['name' => 'admin_nav_shop_reports', 'label' => 'Relatório Shopping', 'route' => 'admin.shop.reports.*', 'match_mode' => 'pattern', 'order' => 327],
            ['name' => 'admin_nav_shop_stock', 'label' => 'Estoque Shopping', 'route' => 'admin.shop.stock.*', 'match_mode' => 'pattern', 'order' => 328],
        ];

        foreach ($rows as $row) {
            Menu::updateOrCreate(
                ['name' => $row['name']],
                [
                    'label' => $row['label'],
                    'route' => $row['route'],
                    'match_mode' => $row['match_mode'],
                    'icon' => null,
                    'order' => $row['order'],
                    'is_required' => false,
                    'parent_id' => null,
                    'portal' => 'admin',
                    'is_container' => false,
                ]
            );
        }
    }

    public function down(): void
    {
        Menu::query()->whereIn('name', [
            'admin_nav_shop',
            'admin_nav_shop_orders',
            'admin_nav_shop_reports',
            'admin_nav_shop_stock',
        ])->delete();
    }
};
