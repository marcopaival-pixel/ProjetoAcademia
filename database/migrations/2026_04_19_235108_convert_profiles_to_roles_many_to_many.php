<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename profiles to roles as requested by the user
        if (Schema::hasTable('profiles') && !Schema::hasTable('roles')) {
            Schema::rename('profiles', 'roles');
        }

        // 2. Rename profile_permissions to role_permissions
        if (Schema::hasTable('profile_permissions') && !Schema::hasTable('role_permissions')) {
            Schema::rename('profile_permissions', 'role_permissions');
            if (Schema::hasColumn('role_permissions', 'profile_id')) {
                Schema::table('role_permissions', function (Blueprint $table) {
                    $table->renameColumn('profile_id', 'role_id');
                });
            }
        }

        // 3. Create user_roles (Many-to-Many)
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedBigInteger('role_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });

            // Migrate existing profile_id from users table to user_roles
            $users = DB::table('users')->whereNotNull('profile_id')->get();
            foreach ($users as $user) {
                // Previne duplicados
                $exists = DB::table('user_roles')
                    ->where('user_id', $user->id)
                    ->where('role_id', $user->profile_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('user_roles')->insert([
                        'user_id' => $user->id,
                        'role_id' => $user->profile_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 4. Ensure "paciente" and "aluno" roles exist
        $roles = [
            ['name' => 'paciente', 'label' => 'Paciente', 'description' => 'Visualiza prescrições e treinos do profissional.'],
            ['name' => 'aluno', 'label' => 'Aluno', 'description' => 'Assinante do sistema com acesso a todas as funcionalidades do plano.'],
        ];

        foreach ($roles as $roleData) {
            $existing = DB::table('roles')->where('name', $roleData['name'])->first();
            if (!$existing) {
                DB::table('roles')->insert(array_merge($roleData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            } else {
                // Update label/description if needed
                DB::table('roles')->where('name', $roleData['name'])->update([
                    'label' => $roleData['label'],
                    'description' => $roleData['description'],
                    'updated_at' => now(),
                ]);
            }
        }

        // 5. Adjust plans table (add features json)
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'features')) {
                $table->json('features')->nullable()->after('price');
            }
        });

        // 6. Subscriptions table is already created in 2026_04_15_180000
    }

    public function down(): void
    {
    }
};
