<?php

namespace Tests\Feature;

use App\Console\Commands\DatabaseTableModelGapCommand;
use App\Models\AcademyCompany;
use App\Models\Menu;
use App\Models\User;
use App\Services\MenuService;
use Database\Seeders\AdminPortalMenusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint11Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_admin_sidebar_shows_shopping_section_for_platform_admin(): void
    {
        $company = AcademyCompany::create(['name' => 'Sidebar Shop', 'slug' => 'sidebar-shop-s11']);

        $admin = User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->seed(AdminPortalMenusSeeder::class);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.products.index'))
            ->assertOk()
            ->assertSee('Produtos do Shopping', false)
            ->assertSee('Shopping', false);
    }

    public function test_aluno_with_academy_company_gets_shopping_menu_items(): void
    {
        $company = AcademyCompany::create(['name' => 'Menu Aluno Shop', 'slug' => 'menu-aluno-s11']);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $groups = app(MenuService::class)->getAccordionMenus($user);
        $labels = collect($groups)
            ->flatMap(fn (array $group) => $group['items'])
            ->pluck('label')
            ->all();

        $this->assertContains('Shopping Fitness', $labels);
        $this->assertContains('Meus Pedidos', $labels);
        $this->assertContains('Pontos & Cashback', $labels);
    }

    public function test_aluno_without_academy_company_has_no_shopping_menu_items(): void
    {
        $user = $this->userWithRole('aluno', [
            'academy_company_id' => null,
            'status' => 'active',
        ]);

        $groups = app(MenuService::class)->getAccordionMenus($user);
        $labels = collect($groups)
            ->flatMap(fn (array $group) => $group['items'])
            ->pluck('label')
            ->all();

        $this->assertNotContains('Shopping Fitness', $labels);
    }

    public function test_table_model_gap_command_audits_shop_prefix(): void
    {
        $report = DatabaseTableModelGapCommand::audit('shop_');

        $this->assertArrayHasKey('tables_without_model', $report);
        $this->assertArrayHasKey('models_without_table', $report);
        $this->assertNotContains('shop_suppliers', $report['tables_without_model']);
        $this->assertNotContains('shop_recommendations', $report['tables_without_model']);
    }

    public function test_shop_admin_portal_menus_exist_after_seeder(): void
    {
        $this->seed(AdminPortalMenusSeeder::class);

        $this->assertDatabaseHas('menus', [
            'name' => 'admin_nav_shop',
            'portal' => 'admin',
        ]);

        $menu = Menu::query()->where('name', 'admin_nav_shop')->firstOrFail();
        $this->assertSame('admin.shop.products.*', $menu->route);
    }
}
