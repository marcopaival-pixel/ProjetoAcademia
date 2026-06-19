<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profession;
use App\Models\Role;
use Illuminate\Support\Str;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professions = [
            ['name' => 'Educador Físico', 'role_name' => 'Educador Físico'],
            ['name' => 'Nutricionista', 'role_name' => 'Nutricionista'],
            ['name' => 'Fisioterapeuta', 'role_name' => 'Fisioterapeuta'],
            ['name' => 'Médico', 'role_name' => 'Médico'],
            ['name' => 'Psicólogo', 'role_name' => 'Psicólogo'],
            ['name' => 'Outro', 'role_name' => 'Outro Profissional'],
        ];

        foreach ($professions as $profData) {
            $profession = Profession::firstOrCreate(
                ['name' => $profData['name']],
                ['slug' => Str::slug($profData['name'])]
            );

            // Create corresponding role to handle permissions if not exists
            Role::firstOrCreate(
                ['name' => Str::slug($profData['role_name'])],
                [
                    'label' => $profData['role_name'],
                    'description' => "Role for " . $profData['name']
                ]
            );
        }
    }
}
