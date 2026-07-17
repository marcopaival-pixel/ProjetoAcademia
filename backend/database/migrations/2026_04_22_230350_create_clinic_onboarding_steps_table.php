<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinic_onboarding_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_company_id')->constrained('academy_companies')->cascadeOnDelete();
            $table->string('step_key'); // e.g., 'registration', 'branding', 'users', etc.
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->json('data')->nullable(); // To store temporary or specific step data
            $table->timestamps();

            $table->unique(['academy_company_id', 'step_key']);
        });

        Schema::table('academy_companies', function (Blueprint $table) {
            $table->string('onboarding_status')->default('pending')->after('is_active'); // pending, in_progress, completed
            $table->unsignedTinyInteger('current_onboarding_step')->default(1)->after('onboarding_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn(['onboarding_status', 'current_onboarding_step']);
        });
        Schema::dropIfExists('clinic_onboarding_steps');
    }
};
