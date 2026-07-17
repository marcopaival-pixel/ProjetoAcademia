<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercadopago_payment_credits', function (Blueprint $table) {
            $table->unsignedBigInteger('mp_payment_id')->primary();
            $table->unsignedInteger('user_id');
            $table->string('plan_code', 16);
            $table->decimal('transaction_amount', 12, 2);
            $table->string('currency_id', 8)->default('BRL');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id'], 'mercadopago_credits_user');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('mercadopago_subscriptions', function (Blueprint $table) {
            $table->string('mp_preapproval_id', 48)->primary();
            $table->unsignedInteger('user_id');
            $table->string('plan_code', 16);
            $table->string('status', 24)->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['user_id'], 'mercadopago_sub_user');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->date('birth_date')->nullable();
            $table->char('sex', 1)->default('');
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->string('activity_level', 32)->default('moderate');
            $table->string('goal', 16)->default('maintain');
            $table->unsignedInteger('daily_calorie_target')->nullable();
            $table->decimal('protein_target_g', 6, 2)->nullable();
            $table->decimal('carbs_target_g', 6, 2)->nullable();
            $table->decimal('fat_target_g', 6, 2)->nullable();
            $table->unsignedSmallInteger('water_target_ml')->nullable()->default(2000);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('food_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('entry_date');
            $table->string('meal_type', 32)->default('other');
            $table->string('food_name', 200);
            $table->unsignedSmallInteger('calories')->default(0);
            $table->decimal('protein_g', 6, 2)->default(0);
            $table->decimal('carbs_g', 6, 2)->default(0);
            $table->decimal('fat_g', 6, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'entry_date'], 'food_entries_user_date');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('exercise_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('entry_date');
            $table->string('activity_type', 120);
            $table->unsignedSmallInteger('duration_min')->default(0);
            $table->unsignedSmallInteger('calories_burned')->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'entry_date'], 'exercise_entries_user_date');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('weight_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('weighed_at');
            $table->decimal('weight_kg', 5, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'weighed_at'], 'weight_entries_user_day');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('water_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('entry_date');
            $table->unsignedSmallInteger('amount_ml')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'entry_date'], 'water_entries_user_date');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_entries');
        Schema::dropIfExists('weight_entries');
        Schema::dropIfExists('exercise_entries');
        Schema::dropIfExists('food_entries');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('mercadopago_subscriptions');
        Schema::dropIfExists('mercadopago_payment_credits');
    }
};
