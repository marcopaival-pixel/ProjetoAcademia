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
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Seed das profissões iniciais sugeridas
        $professions = [
            ['name' => 'Personal Trainer', 'slug' => 'personal-trainer'],
            ['name' => 'Nutricionista', 'slug' => 'nutricionista'],
            ['name' => 'Fisioterapeuta', 'slug' => 'fisioterapeuta'],
            ['name' => 'Médico', 'slug' => 'medico'],
            ['name' => 'Psicólogo', 'slug' => 'psicologo'],
        ];

        foreach ($professions as $p) {
            DB::table('professions')->insert(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->foreignId('profession_id')->constrained('professions');
            $table->string('specialty')->nullable();
            
            // Registro Profissional
            $table->string('registration_number');
            $table->string('council'); // CRM, CREF, etc.
            $table->string('registration_uf', 2);
            $table->date('registration_expiry_date');
            
            // Documentação
            $table->string('document_path')->nullable(); // storage/profissionais/documentos/
            $table->text('signature_path')->nullable(); // storage/profissionais/assinaturas/ (ou base64 se drawn)
            
            // Auditoria
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->integer('document_version')->default(1);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_profiles');
        Schema::dropIfExists('professions');
    }
};
