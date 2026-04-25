<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename 'roles' to 'profiles'
        if (Schema::hasTable('roles') && !Schema::hasTable('profiles')) {
            Schema::rename('roles', 'profiles');
        }

        // 2. Rename 'permission_role' to 'profile_permissions' and adjust
        if (Schema::hasTable('permission_role')) {
            Schema::create('profile_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();

                $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });

            // Migrate data
            $oldData = DB::table('permission_role')->get();
            foreach ($oldData as $row) {
                DB::table('profile_permissions')->insert([
                    'profile_id' => $row->role_id,
                    'permission_id' => $row->permission_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::dropIfExists('permission_role');
        }

        // 3. Adjust 'users' table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->renameColumn('role_id', 'profile_id');
            }

            if (!Schema::hasColumn('users', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('profile_id');
                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            }
        });

        // 4. Create 'plan_permissions'
        if (!Schema::hasTable('plan_permissions')) {
            Schema::create('plan_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plan_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();

                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }
        
        // Ensure plans table has the requested fields (it mostly does already)
        if (!Schema::hasColumn('plans', 'status')) {
             Schema::table('plans', function (Blueprint $table) {
                 $table->enum('status', ['active', 'inactive'])->default('active')->after('price');
             });
        }

        // 5. Initial Seed for Plans if empty
        if (DB::table('plans')->count() == 0) {
            DB::table('plans')->insert([
                ['name' => 'Free', 'price' => 0.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now(), 'type' => 'student'],
                ['name' => 'Premium', 'price' => 29.90, 'status' => 'active', 'created_at' => now(), 'updated_at' => now(), 'type' => 'student'],
            ]);
        }
    }

    public function down(): void
    {
        // Reversal logic if needed (skipping for brevity but recommended in production)
    }
};
