<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas que receberão o professional_id
     */
    protected $tables = [
        'training_plans',
        'body_assessments',
        'weight_entries',
        'water_entries',
        'medical_evolutions',
        'goals',
        'smart_stacks'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'professional_id')) {
                        $column = $table->unsignedInteger('professional_id')->nullable();
                        if (Schema::hasColumn($tableName, 'user_id')) {
                            $column->after('user_id');
                        }
                        
                        $table->foreign('professional_id')
                            ->references('id')
                            ->on('users')
                            ->nullOnDelete();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'professional_id')) {
                        $table->dropForeign(['professional_id']);
                        $table->dropColumn('professional_id');
                    }
                });
            }
        }
    }
};
