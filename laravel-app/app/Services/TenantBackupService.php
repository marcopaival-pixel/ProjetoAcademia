<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class TenantBackupService
{
    /**
     * Tables that have academy_company_id directly.
     */
    protected array $directTables = [
        'academy_units',
        'clinic_onboarding_steps',
        'clinic_protocols',
        'financial_logs',
        'historico_pdfs',
        'medical_prescriptions',
        'menu_permission_audit_logs',
        'omni_agents',
        'omni_bots',
        'omni_business_hours',
        'omni_channels',
        'omni_chatbot_rules',
        'omni_conversations',
        'omni_queues',
        'pdf_number_sequences',
        'pdf_templates',
        'role_menu_permissions',
        'subscriptions',
        'users'
    ];

    /**
     * Tables that don't have academy_company_id but are linked via user_id.
     */
    protected array $userRelatedTables = [
        'user_profiles',
        'training_plans',
        'meal_templates',
        'body_assessments',
        'exercise_entries',
        'food_entries',
        'water_entries',
        'weight_entries',
        'ai_chats',
        'evolution_photos',
        'workout_sessions',
        'load_logs',
        'achievements',
        'user_achievements',
        'user_plans'
    ];

    public function export(int $companyId)
    {
        $backupData = [
            'metadata' => [
                'company_id' => $companyId,
                'exported_at' => now()->toDateTimeString(),
                'version' => '1.1'
            ],
            'tables' => []
        ];

        // 1. Export direct tables
        foreach ($this->directTables as $table) {
            if (Schema::hasTable($table)) {
                $column = Schema::hasColumn($table, 'academy_company_id') ? 'academy_company_id' : 'company_id';
                
                $backupData['tables'][$table] = DB::table($table)
                    ->where($column, $companyId)
                    ->get()
                    ->toArray();
            }
        }

        // 2. Export user-related tables
        $userIds = DB::table('users')
            ->where('academy_company_id', $companyId)
            ->pluck('id')
            ->toArray();

        if (!empty($userIds)) {
            foreach ($this->userRelatedTables as $table) {
                if (Schema::hasTable($table)) {
                    $backupData['tables'][$table] = DB::table($table)
                        ->whereIn('user_id', $userIds)
                        ->get()
                        ->toArray();
                }
            }
        }

        $fileName = "tenant_backup_{$companyId}_" . now()->format('Ymd_His') . ".json";
        $filePath = "backups/tenants/{$companyId}/" . $fileName;
        
        Storage::put($filePath, json_encode($backupData, JSON_PRETTY_PRINT));

        return [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'tables_count' => count($backupData['tables'])
        ];
    }

    public function restore(int $companyId, string $filePath)
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception("Ficheiro de backup não encontrado.");
        }

        $data = json_decode(Storage::get($filePath), true);
        
        if ($data['metadata']['company_id'] != $companyId) {
            throw new \Exception("Este backup pertence a outra empresa (ID: {$data['metadata']['company_id']}).");
        }

        return DB::transaction(function () use ($companyId, $data) {
            Schema::disableForeignKeyConstraints();

            try {
                $userIds = DB::table('users')
                    ->where('academy_company_id', $companyId)
                    ->pluck('id')
                    ->toArray();

                if (!empty($userIds)) {
                    foreach (array_reverse($this->userRelatedTables) as $table) {
                        if (Schema::hasTable($table)) {
                            DB::table($table)->whereIn('user_id', $userIds)->delete();
                        }
                    }
                }

                foreach (array_reverse($this->directTables) as $table) {
                    if (Schema::hasTable($table)) {
                        $column = Schema::hasColumn($table, 'academy_company_id') ? 'academy_company_id' : 'company_id';
                        DB::table($table)->where($column, $companyId)->delete();
                    }
                }

                foreach ($this->directTables as $table) {
                    if (isset($data['tables'][$table]) && !empty($data['tables'][$table])) {
                        DB::table($table)->insert($data['tables'][$table]);
                    }
                }

                foreach ($this->userRelatedTables as $table) {
                    if (isset($data['tables'][$table]) && !empty($data['tables'][$table])) {
                        DB::table($table)->insert($data['tables'][$table]);
                    }
                }

                Log::info("Tenant Restore Successful: Company ID {$companyId}");
                return true;

            } catch (\Exception $e) {
                Log::error("Tenant Restore Failed: " . $e->getMessage());
                throw $e;
            } finally {
                Schema::enableForeignKeyConstraints();
            }
        });
    }
}
