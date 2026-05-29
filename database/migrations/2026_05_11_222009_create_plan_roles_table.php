<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['plan_id', 'role_id']);
        });

        // Seed inicial: mapear planos existentes via plans.type → roles
        $typeToRole = [
            'student'      => 'aluno',
            'professional' => 'professional',
            'clinic'       => 'academia',
        ];

        foreach ($typeToRole as $planType => $roleName) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            $plans = DB::table('plans')->where('type', $planType)->get();
            foreach ($plans as $plan) {
                $exists = DB::table('plan_roles')
                    ->where('plan_id', $plan->id)
                    ->where('role_id', $role->id)
                    ->exists();

                if (! $exists) {
                    DB::table('plan_roles')->insert([
                        'plan_id'    => $plan->id,
                        'role_id'    => $role->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_roles');
    }
};
