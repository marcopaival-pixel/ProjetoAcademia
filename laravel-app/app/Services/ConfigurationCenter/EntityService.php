<?php

namespace App\Services\ConfigurationCenter;

use App\Models\AdminEntity;
use App\Models\AdminField;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EntityService
{
    public function createEntity(array $data, array $fields = [])
    {
        return DB::transaction(function () use ($data, $fields) {
            $entity = AdminEntity::create($data);

            foreach ($fields as $field) {
                $entity->fields()->create($field);
            }

            return $entity;
        });
    }

    public function updateEntity(AdminEntity $entity, array $data)
    {
        return $entity->update($data);
    }

    public function getUnregisteredTables()
    {
        $tables = Schema::getTables();
        $allTableNames = array_map(fn($table) => $table['name'], $tables);
        
        $registeredTables = AdminEntity::pluck('table_name')->toArray();
        
        $excludedTables = [
            'migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens',
            'admin_entities', 'admin_fields', 'audit_logs', 'record_versions', 'sessions', 'cache', 'jobs', 'job_batches'
        ];

        return array_diff($allTableNames, array_merge($registeredTables, $excludedTables));
    }

    public function autoRegisterTable(string $tableName)
    {
        return DB::transaction(function () use ($tableName) {
            // 1. Tentar descobrir o Model
            $modelName = Str::studly(Str::singular($tableName));
            $modelClass = "App\\Models\\{$modelName}";
            
            if (!class_exists($modelClass)) {
                // Tentar sem o plural se falhar
                $modelName = Str::studly($tableName);
                $modelClass = "App\\Models\\{$modelName}";
            }

            // 2. Criar Entidade
            $entity = AdminEntity::create([
                'name' => $tableName,
                'display_name' => Str::title(str_replace('_', ' ', $tableName)),
                'table_name' => $tableName,
                'model_class' => class_exists($modelClass) ? $modelClass : 'App\Models\GenericModel',
                'icon' => 'box',
                'category' => 'Autodiscovered',
                'is_active' => true,
            ]);

            // 3. Criar Campos
            $columns = Schema::getColumnListing($tableName);
            $sortOrder = 1;

            foreach ($columns as $column) {
                if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) continue;

                $type = Schema::getColumnType($tableName, $column);
                $inputType = $this->mapSqlToInputType($type, $column);

                AdminField::create([
                    'admin_entity_id' => $entity->id,
                    'name' => $column,
                    'label' => Str::title(str_replace('_', ' ', $column)),
                    'type' => $inputType,
                    'is_required' => false, // Default
                    'is_visible_list' => true,
                    'is_visible_form' => true,
                    'sort_order' => $sortOrder++,
                ]);
            }

            return $entity;
        });
    }

    protected function mapSqlToInputType(string $sqlType, string $columnName)
    {
        if (Str::contains($columnName, ['is_', 'has_', 'active'])) return 'boolean';
        if (Str::contains($columnName, ['date', '_at'])) return 'date';
        if (Str::contains($columnName, ['description', 'instructions', 'content', 'payload', 'text'])) return 'textarea';
        
        switch ($sqlType) {
            case 'integer':
            case 'bigint':
            case 'decimal':
            case 'float':
                return 'number';
            case 'boolean':
                return 'boolean';
            case 'text':
            case 'longtext':
                return 'textarea';
            default:
                return 'text';
        }
    }

    public function syncFields(AdminEntity $entity, array $fields)
    {
        return DB::transaction(function () use ($entity, $fields) {
            $existingIds = collect($fields)->pluck('id')->filter()->toArray();
            $entity->fields()->whereNotIn('id', $existingIds)->delete();

            foreach ($fields as $index => $fieldData) {
                $fieldData['sort_order'] = $index;
                if (isset($fieldData['id'])) {
                    $field = AdminField::find($fieldData['id']);
                    $field->update($fieldData);
                } else {
                    $entity->fields()->create($fieldData);
                }
            }
        });
    }
}
