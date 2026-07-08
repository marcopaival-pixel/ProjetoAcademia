<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Evolui a pivot organization_user para suportar contexto multi-tenant.
     *
     * A tabela base ja e criada por 2026_05_03_130002_create_organization_user_table.php.
     * Esta migration nao deve recria-la em bancos novos nem derruba-la em rollback parcial.
     */
    public function up(): void
    {
        if (! Schema::hasTable('organization_user')) {
            Schema::create('organization_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('organization_id');
                $table->string('role')->default('paciente');
                $table->boolean('is_active')->default(true);
                $table->date('joined_at')->nullable();
                $table->string('position')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'organization_id', 'role']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
                $table->index(['organization_id', 'role'], 'organization_user_organization_id_role_index');
                $table->index(['user_id', 'is_active'], 'organization_user_user_id_is_active_index');
            });

            return;
        }

        Schema::table('organization_user', function (Blueprint $table) {
            if (! Schema::hasColumn('organization_user', 'joined_at')) {
                $table->date('joined_at')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('organization_user', 'position')) {
                $table->string('position')->nullable()->after('joined_at');
            }
        });

        $this->addIndexIfMissing('organization_user', ['organization_id', 'role'], 'organization_user_organization_id_role_index');
        $this->addIndexIfMissing('organization_user', ['user_id', 'is_active'], 'organization_user_user_id_is_active_index');
    }

    public function down(): void
    {
        if (! Schema::hasTable('organization_user')) {
            return;
        }

        Schema::table('organization_user', function (Blueprint $table) {
            if ($this->indexExists('organization_user', 'organization_user_organization_id_role_index')) {
                $table->dropIndex('organization_user_organization_id_role_index');
            }

            if ($this->indexExists('organization_user', 'organization_user_user_id_is_active_index')) {
                $table->dropIndex('organization_user_user_id_is_active_index');
            }
        });

        Schema::table('organization_user', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('organization_user', 'position') ? 'position' : null,
                Schema::hasColumn('organization_user', 'joined_at') ? 'joined_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * @param  list<string>  $columns
     */
    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $indexName) {
                return true;
            }
        }

        return false;
    }
};
