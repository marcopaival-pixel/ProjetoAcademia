<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Internal name (e.g. 'exercises')
            $table->string('display_name'); // Label (e.g. 'Exercícios')
            $table->string('table_name'); // DB table name
            $table->string('model_class'); // Fully qualified model name
            $table->text('description')->nullable();
            $table->string('icon')->default('heroicon-o-cube');
            $table->string('category')->default('General');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // Extra configs (e.g. per_page, default_sort)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_entities');
    }
};
