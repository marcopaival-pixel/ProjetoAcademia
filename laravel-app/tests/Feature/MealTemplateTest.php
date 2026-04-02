<?php

namespace Tests\Feature;

use App\Models\MealTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MealTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_meal_template_rejected_without_premium_access(): void
    {
        $user = User::factory()->create(['is_premium' => false, 'is_admin' => false]);
        $date = '2026-04-10';
        DB::table('food_entries')->insert([
            'user_id' => $user->id,
            'entry_date' => $date,
            'meal_type' => 'lunch',
            'food_name' => 'Salada',
            'calories' => 150,
            'protein_g' => 5,
            'carbs_g' => 10,
            'fat_g' => 8,
            'created_at' => now(),
        ]);

        $this->actingAs($user)->post(route('diary'), [
            'action' => 'save_meal_template',
            'template_name' => 'Não pode',
            'template_source_date' => $date,
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('meal_templates', 0);
    }

    public function test_premium_user_saves_and_applies_meal_template(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);
        $date = '2026-04-10';
        DB::table('food_entries')->insert([
            'user_id' => $user->id,
            'entry_date' => $date,
            'meal_type' => 'lunch',
            'food_name' => 'Arroz e feijão',
            'calories' => 400,
            'protein_g' => 12,
            'carbs_g' => 70,
            'fat_g' => 6,
            'created_at' => now(),
        ]);

        $this->actingAs($user)->post(route('diary'), [
            'action' => 'save_meal_template',
            'template_name' => 'Almoço base',
            'template_source_date' => $date,
        ])->assertRedirect(route('diary', ['date' => $date, 'flash' => 'template_saved']));

        $this->assertDatabaseHas('meal_templates', ['user_id' => $user->id, 'name' => 'Almoço base']);

        $tid = (int) MealTemplate::query()->where('user_id', $user->id)->value('id');
        $target = '2026-04-15';

        $this->actingAs($user)->post(route('diary'), [
            'action' => 'apply_meal_template',
            'meal_template_id' => $tid,
            'target_date' => $target,
        ])->assertRedirect(route('diary', ['date' => $target, 'flash' => 'template_applied', 'n' => 1]));

        $this->assertDatabaseHas('food_entries', [
            'user_id' => $user->id,
            'entry_date' => $target,
            'food_name' => 'Arroz e feijão',
            'calories' => 400,
        ]);
    }

    public function test_administrator_can_save_meal_template_without_subscription(): void
    {
        $user = User::factory()->administrator()->create(['is_premium' => false]);
        $date = '2026-05-01';
        DB::table('food_entries')->insert([
            'user_id' => $user->id,
            'entry_date' => $date,
            'meal_type' => 'breakfast',
            'food_name' => 'Aveia',
            'calories' => 200,
            'protein_g' => 8,
            'carbs_g' => 30,
            'fat_g' => 4,
            'created_at' => now(),
        ]);

        $this->actingAs($user)->post(route('diary'), [
            'action' => 'save_meal_template',
            'template_name' => 'Café pista',
            'template_source_date' => $date,
        ])->assertRedirect(route('diary', ['date' => $date, 'flash' => 'template_saved']));

        $this->assertDatabaseHas('meal_templates', ['user_id' => $user->id, 'name' => 'Café pista']);
    }
}
