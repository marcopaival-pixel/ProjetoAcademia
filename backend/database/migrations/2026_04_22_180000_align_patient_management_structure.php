<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename table and columns to match suggested structure
        if (Schema::hasTable('paciente_profissional')) {
            Schema::rename('paciente_profissional', 'pacientes');
        }

        if (Schema::hasTable('pacientes')) {
            Schema::table('pacientes', function (Blueprint $table) {
                if (Schema::hasColumn('pacientes', 'paciente_id')) {
                    $table->renameColumn('paciente_id', 'user_id');
                }
                if (Schema::hasColumn('pacientes', 'data_vinculo')) {
                    $table->renameColumn('data_vinculo', 'data_cadastro');
                }
            });

            // Use statement for 'status' to avoid enum rename issues with MariaDB/Laravel
            if (Schema::hasColumn('pacientes', 'ativo')) {
                DB::statement("ALTER TABLE pacientes CHANGE COLUMN ativo status ENUM('Sim', 'Não') DEFAULT 'Sim'");
            }
        }

        // 2. Ensure roles exist and match labels
        $roles = [
            ['name' => 'aluno', 'label' => 'Aluno'],
            ['name' => 'paciente', 'label' => 'Paciente'],
            ['name' => 'professional', 'label' => 'Profissional'],
            ['name' => 'receptionist', 'label' => 'Recepção'],
            ['name' => 'admin', 'label' => 'Administrador'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                ['label' => $role['label']]
            );
        }

        // 3. Table for transfer history
        Schema::create('patient_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('from_professional_id');
            $table->unsignedInteger('to_professional_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_professional_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_transfers');
        
        if (Schema::hasTable('pacientes')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->renameColumn('user_id', 'paciente_id');
                $table->renameColumn('data_cadastro', 'data_vinculo');
                $table->renameColumn('status', 'ativo');
            });
            Schema::rename('pacientes', 'paciente_profissional');
        }
    }
};
