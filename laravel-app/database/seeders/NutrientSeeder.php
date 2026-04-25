<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nutrient;

class NutrientSeeder extends Seeder
{
    public function run(): void
    {
        $nutrients = [
            // Macros
            ['slug' => 'energy_kcal',   'name' => 'Calorias',       'unit' => 'kcal', 'is_main' => true],
            ['slug' => 'protein_g',     'name' => 'Proteínas',      'unit' => 'g',    'is_main' => true],
            ['slug' => 'carbohydrates_g','name' => 'Carboidratos',   'unit' => 'g',    'is_main' => true],
            ['slug' => 'fat_g',         'name' => 'Gorduras Totais','unit' => 'g',    'is_main' => true],
            
            // Detalhes de Gordura e Fibras
            ['slug' => 'fat_saturated_g','name' => 'Gorduras Saturadas', 'unit' => 'g', 'is_main' => false],
            ['slug' => 'fat_trans_g',    'name' => 'Gorduras Trans',     'unit' => 'g', 'is_main' => false],
            ['slug' => 'fiber_g',        'name' => 'Fibras',             'unit' => 'g', 'is_main' => false],
            ['slug' => 'sugars_g',       'name' => 'Açúcares',           'unit' => 'g', 'is_main' => false],
            
            // Minerais e Sódio
            ['slug' => 'sodium_mg',      'name' => 'Sódio',              'unit' => 'mg', 'is_main' => false],
            ['slug' => 'calcium_mg',     'name' => 'Cálcio',             'unit' => 'mg', 'is_main' => false],
            ['slug' => 'iron_mg',        'name' => 'Ferro',              'unit' => 'mg', 'is_main' => false],
            ['slug' => 'potassium_mg',   'name' => 'Potássio',           'unit' => 'mg', 'is_main' => false],
            
            // Vitaminas
            ['slug' => 'vitamin_c_mg',   'name' => 'Vitamina C',         'unit' => 'mg',  'is_main' => false],
            ['slug' => 'vitamin_a_mcg',  'name' => 'Vitamina A',         'unit' => 'mcg', 'is_main' => false],
            ['slug' => 'vitamin_d_mcg',  'name' => 'Vitamina D',         'unit' => 'mcg', 'is_main' => false],
        ];

        foreach ($nutrients as $n) {
            Nutrient::updateOrCreate(['slug' => $n['slug']], $n);
        }
    }
}
